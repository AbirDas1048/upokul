<?php

namespace Tests\Feature;

use App\Jobs\SendContactMailJob;
use App\Mail\ContactMailMailable;
use App\Mail\ContactThankYouMailable;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
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
        // This test uses the real (array) mail transport, which validates addresses, so it must
        // not depend on the environment: CI builds .env from .env.example where OFFICE_EMAIL is
        // the non-address placeholder "OFFICE_EMAIL".
        config(['custom.office_email' => 'office@example.com']);

        $contact = $this->makeContact();
        $this->assertSame(ContactService::MAIL_SEND_STATUS_PENDING, $contact->status);

        // Read the row from the database at the exact moment the message is sent.
        $statusWhileSending = null;
        Event::listen(MessageSending::class, function () use ($contact, &$statusWhileSending) {
            $statusWhileSending ??= ContactMail::find($contact->id)->status;
        });

        (new SendContactMailJob($contact))->handle();

        $this->assertSame(ContactService::MAIL_SEND_STATUS_PROCESSING, $statusWhileSending);
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

    public function test_row_already_being_processed_is_not_claimed_by_a_second_copy(): void
    {
        Mail::fake();

        $contact = $this->makeContact();
        // Another worker has just claimed it (fresh Processing).
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_PROCESSING]);

        (new SendContactMailJob($contact))->handle();

        Mail::assertNothingSent();
        $this->assertSame(ContactService::MAIL_SEND_STATUS_PROCESSING, $contact->fresh()->status);
    }

    public function test_two_copies_of_the_job_send_only_one_owner_mail(): void
    {
        Mail::fake();
        config(['custom.office_email' => 'office@example.com']);

        $contact = $this->makeContact();

        // Both copies were deserialized while the row was still Pending...
        $first = new SendContactMailJob($contact);
        $second = new SendContactMailJob(ContactMail::find($contact->id));

        $first->handle();
        $second->handle();

        Mail::assertSent(ContactMailMailable::class, 1);
    }

    public function test_stale_processing_row_from_a_crashed_worker_can_be_claimed_again(): void
    {
        Mail::fake();
        config(['custom.office_email' => 'office@example.com']);

        $contact = $this->makeContact();
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_PROCESSING]);
        ContactMail::whereKey($contact->id)->update([
            'updated_at' => now()->subMinutes(SendContactMailJob::STALE_PROCESSING_MINUTES + 1),
        ]);

        (new SendContactMailJob($contact->fresh()))->handle();

        Mail::assertSent(ContactMailMailable::class, 1);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $contact->fresh()->status);
    }

    public function test_failed_row_can_be_claimed_again_on_retry(): void
    {
        Mail::fake();
        config(['custom.office_email' => 'office@example.com']);

        $contact = $this->makeContact();
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_FAILED, 'error_message' => 'SMTP down']);

        (new SendContactMailJob($contact->fresh()))->handle();

        Mail::assertSent(ContactMailMailable::class, 1);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $contact->fresh()->status);
        $this->assertNull($contact->fresh()->error_message);
    }
}
