<?php

namespace Tests\Feature;

use App\Jobs\SendContactMailJob;
use App\Mail\ContactMailMailable;
use App\Mail\ContactThankYouMailable;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\DB;
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

    /**
     * Runs the job the way a worker does: with a queue job whose attempt number is $attempt.
     *
     * @param  int|false|null  $expectRelease  int = must be released with exactly that delay,
     *                                         false = must NOT be released, null = don't care
     */
    private function runAsQueueAttempt(ContactMail $contact, int $attempt, int|false|null $expectRelease = null): void
    {
        $queueJob = \Mockery::mock(Job::class);
        $queueJob->shouldReceive('attempts')->andReturn($attempt);

        match (true) {
            $expectRelease === false => $queueJob->shouldReceive('release')->never(),
            is_int($expectRelease) => $queueJob->shouldReceive('release')->once()->with($expectRelease),
            default => $queueJob->shouldReceive('release')->andReturnNull(),
        };

        $job = new SendContactMailJob($contact);
        $job->setJob($queueJob);
        $job->handle();
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
            'updated_at' => now()->subSeconds(SendContactMailJob::STALE_PROCESSING_SECONDS + 5),
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

    public function test_failed_refresh_after_claim_leaves_a_retryable_failed_row(): void
    {
        Mail::fake();
        config(['custom.office_email' => 'office@example.com']);

        $contact = $this->makeContact();
        $job = new SendContactMailJob($contact);

        // The database read inside the job (refresh) fails once, after the row was claimed.
        $dbIsDown = true;
        ContactMail::retrieved(function () use (&$dbIsDown) {
            if ($dbIsDown) {
                throw new \RuntimeException('db read failed');
            }
        });

        try {
            $job->handle();
            $this->fail('Exception expected so the queue retries the job');
        } catch (\RuntimeException $e) {
            $this->assertSame('db read failed', $e->getMessage());
        }

        // Must NOT be left in Processing (a retry could not claim it and the mail would be lost).
        $row = DB::table('contact_mails')->where('id', $contact->id)->first();
        $this->assertSame(ContactService::MAIL_SEND_STATUS_FAILED, (int) $row->status);
        $this->assertSame('db read failed', $row->error_message);
        Mail::assertNothingSent();

        // The retry (database is back) can claim the row and delivers exactly once.
        $dbIsDown = false;
        (new SendContactMailJob($contact))->handle();

        Mail::assertSent(ContactMailMailable::class, 1);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $contact->fresh()->status);
    }

    public function test_redelivered_job_recovers_the_claim_of_a_worker_that_crashed(): void
    {
        Mail::fake();
        config(['custom.office_email' => 'office@example.com']);

        // A worker claimed the row and died without sending. The queue redelivers the message after
        // retry_after (90s), by which time that claim is 90s old - older than the 75s stale window.
        $contact = $this->makeContact();
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_PROCESSING]);
        ContactMail::whereKey($contact->id)->update(['updated_at' => now()->subSeconds(90)]);

        $this->runAsQueueAttempt($contact->fresh(), 2);

        Mail::assertSent(ContactMailMailable::class, 1);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $contact->fresh()->status);
    }

    public function test_first_attempt_leaves_another_workers_fresh_processing_row_alone(): void
    {
        Mail::fake();

        $contact = $this->makeContact();
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_PROCESSING]);

        $this->runAsQueueAttempt($contact->fresh(), 1);

        Mail::assertNothingSent();
    }

    public function test_a_retry_of_an_older_message_cannot_take_over_a_live_claim_of_another_message(): void
    {
        Mail::fake();

        // Message A failed and is waiting for its queue retry. `contact:resend-failed` reset the row and
        // queued message B, whose first attempt has just claimed it (a live, 10-second-old claim).
        $contact = $this->makeContact();
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_PROCESSING]);
        ContactMail::whereKey($contact->id)->update(['updated_at' => now()->subSeconds(10)]);

        // A's retry has attempts() > 1 but must NOT touch B's live claim (that would send the mail twice).
        $this->runAsQueueAttempt($contact->fresh(), 2);

        Mail::assertNothingSent();
        $this->assertSame(ContactService::MAIL_SEND_STATUS_PROCESSING, $contact->fresh()->status);
    }

    public function test_stale_window_sits_between_the_job_timeout_and_the_queue_redelivery_delay(): void
    {
        $timeout = (new SendContactMailJob($this->makeContact()))->timeout;
        $retryAfter = (int) config('queue.connections.database.retry_after');

        // A live attempt (<= timeout) must never look abandoned, and a crashed one must already look
        // abandoned when the queue redelivers the message.
        $this->assertLessThan(SendContactMailJob::STALE_PROCESSING_SECONDS, $timeout);
        $this->assertLessThan($retryAfter, SendContactMailJob::STALE_PROCESSING_SECONDS);
    }

    public function test_job_blocked_by_a_live_claim_is_released_so_it_can_recover_if_that_worker_crashes(): void
    {
        Mail::fake();

        // Another attempt claimed the row 20s ago and is (as far as we can tell) still working on it.
        $contact = $this->makeContact();
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_PROCESSING]);
        ContactMail::whereKey($contact->id)->update(['updated_at' => now()->subSeconds(20)]);

        // We must not send (the live claim is respected) and must not simply end (the job would be
        // deleted and, if that worker then crashes, the enquiry lost): release for a later attempt that
        // lands after the claim has gone stale.
        $this->runAsQueueAttempt($contact->fresh(), 1, SendContactMailJob::STALE_PROCESSING_SECONDS + 5);

        Mail::assertNothingSent();
    }

    public function test_job_is_not_released_when_the_contact_is_already_completed(): void
    {
        Mail::fake();

        $contact = $this->makeContact();
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_SUCCESS]);

        $this->runAsQueueAttempt($contact->fresh(), 1, false);

        Mail::assertNothingSent();
    }

    public function test_released_job_sends_once_the_blocking_claim_has_gone_stale(): void
    {
        Mail::fake();
        config(['custom.office_email' => 'office@example.com']);

        $contact = $this->makeContact();
        $contact->update(['status' => ContactService::MAIL_SEND_STATUS_PROCESSING]);
        ContactMail::whereKey($contact->id)->update(['updated_at' => now()->subSeconds(20)]);

        $this->runAsQueueAttempt($contact->fresh(), 1);            // blocked, released
        Mail::assertNothingSent();

        // ...the other worker died; by the time the released job runs again the claim is stale.
        ContactMail::whereKey($contact->id)->update(['updated_at' => now()->subSeconds(SendContactMailJob::STALE_PROCESSING_SECONDS + 5)]);
        $this->runAsQueueAttempt($contact->fresh(), 2, false);

        Mail::assertSent(ContactMailMailable::class, 1);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $contact->fresh()->status);
    }
}
