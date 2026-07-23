<?php

namespace Tests\Unit;

use App\Jobs\SendContactMailJob;
use App\Mail\ContactMailMailable;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class SendContactMailJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_contact_mail_and_marks_contact_as_sent(): void
    {
        Mail::fake();
        config(['custom.office_email' => 'office@example.com']);
        $contactMail = $this->createContactMail();

        (new SendContactMailJob($contactMail))->handle();

        Mail::assertSent(ContactMailMailable::class, function (ContactMailMailable $mail) use ($contactMail) {
            return $mail->contactMail->is($contactMail)
                && $mail->hasTo('office@example.com');
        });

        $contactMail->refresh();
        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $contactMail->status);
        $this->assertNotNull($contactMail->sent_at);
        $this->assertNull($contactMail->error_message);
    }

    public function test_job_uses_expected_retry_configuration(): void
    {
        $contactMail = $this->createContactMail();
        $job = new SendContactMailJob($contactMail);

        $this->assertSame(3, $job->tries);
        $this->assertSame(10, $job->backoff);
        $this->assertTrue($job->contactMail->is($contactMail));
    }

    public function test_job_marks_contact_as_failed_when_mail_send_throws_exception(): void
    {
        config(['custom.office_email' => 'office@example.com']);
        $contactMail = $this->createContactMail();

        Mail::shouldReceive('to')
            ->once()
            ->with('office@example.com')
            ->andThrow(new RuntimeException('SMTP server unavailable'));

        try {
            (new SendContactMailJob($contactMail))->handle();

            $this->fail('Expected the contact mail job to rethrow the mail exception.');
        } catch (RuntimeException $exception) {
            $this->assertSame('SMTP server unavailable', $exception->getMessage());
        }

        $contactMail->refresh();
        $this->assertSame(ContactService::MAIL_SEND_STATUS_FAILED, $contactMail->status);
        $this->assertNull($contactMail->sent_at);
        $this->assertSame('SMTP server unavailable', $contactMail->error_message);
    }

    private function createContactMail(): ContactMail
    {
        return ContactMail::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '01700000000',
            'subject' => 'Booking Question',
            'mail_message' => 'I want to know more about your service.',
            'request_source' => ['ip' => '127.0.0.1'],
            'status' => ContactService::MAIL_SEND_STATUS_PENDING,
        ]);
    }
}
