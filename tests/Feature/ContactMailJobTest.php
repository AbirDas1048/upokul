<?php

namespace Tests\Feature;

use App\Jobs\SendContactMailJob;
use App\Mail\ContactMailMailable;
use App\Mail\ContactThankYouMailable;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactMailJobTest extends TestCase
{
    use RefreshDatabase;

    private function makeContact(): ContactMail
    {
        return ContactMail::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'phone' => '01700000000',
            'subject' => 'Tour inquiry',
            'mail_message' => 'I want to book a tour.',
            'request_source' => [],
            'status' => ContactService::MAIL_SEND_STATUS_PENDING,
        ]);
    }

    public function test_job_sends_owner_mail_and_customer_thank_you(): void
    {
        Mail::fake();
        config(['custom.office_email' => 'office@example.com']);

        $contact = $this->makeContact();
        (new SendContactMailJob($contact))->handle();

        Mail::assertSent(ContactMailMailable::class, function ($mail) {
            return $mail->hasTo('office@example.com')
                && $mail->hasReplyTo('demo@example.com')
                && str_contains($mail->render(), 'I want to book a tour.');
        });

        Mail::assertSent(ContactThankYouMailable::class, function ($mail) {
            return $mail->hasTo('demo@example.com')
                && str_contains($mail->render(), 'Demo User');
        });

        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $contact->fresh()->status);
        $this->assertNotNull($contact->fresh()->sent_at);
    }

    public function test_status_is_processing_while_sending_then_completed(): void
    {
        $contact = $this->makeContact();
        $this->assertSame(ContactService::MAIL_SEND_STATUS_PENDING, $contact->status);

        $seen = null;
        Mail::fake();

        // Record that the row passed through Processing (3) before Completed (1).
        ContactMail::updating(function ($model) use (&$seen) {
            if ($model->status === ContactService::MAIL_SEND_STATUS_PROCESSING) {
                $seen = ContactService::MAIL_SEND_STATUS_PROCESSING;
            }
        });

        (new SendContactMailJob($contact))->handle();

        $this->assertSame(ContactService::MAIL_SEND_STATUS_PROCESSING, $seen);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $contact->fresh()->status);
    }

    public function test_failed_send_sets_failed_status_with_error(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $contact = $this->makeContact();

        try {
            (new SendContactMailJob($contact))->handle();
            $this->fail('Exception expected');
        } catch (\RuntimeException) {
        }

        $fresh = $contact->fresh();
        $this->assertSame(ContactService::MAIL_SEND_STATUS_FAILED, $fresh->status);
        $this->assertSame('SMTP down', $fresh->error_message);
    }

    public function test_completed_contact_is_not_sent_again(): void
    {
        Mail::fake();

        $contact = $this->makeContact();
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_SUCCESS]);

        (new SendContactMailJob($contact))->handle();

        Mail::assertNothingSent();
    }
}
