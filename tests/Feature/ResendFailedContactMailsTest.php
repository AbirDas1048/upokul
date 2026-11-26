<?php

namespace Tests\Feature;

use App\Jobs\SendContactMailJob;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ResendFailedContactMailsTest extends TestCase
{
    use RefreshDatabase;

    private function make(int $status): ContactMail
    {
        return ContactMail::create([
            'name' => 'A', 'email' => 'a@example.com', 'phone' => '1', 'subject' => 'S',
            'mail_message' => 'M', 'request_source' => [], 'status' => $status,
            'error_message' => $status === ContactService::MAIL_SEND_STATUS_FAILED ? 'boom' : null,
        ]);
    }

    public function test_failed_rows_are_reset_and_requeued(): void
    {
        Queue::fake();

        $failed = $this->make(ContactService::MAIL_SEND_STATUS_FAILED);
        $done = $this->make(ContactService::MAIL_SEND_STATUS_SUCCESS);

        $this->artisan('contact:resend-failed')->expectsOutput('Re-queued 1 contact mail(s).')->assertSuccessful();

        Queue::assertPushed(SendContactMailJob::class, 1);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_PENDING, $failed->fresh()->status);
        $this->assertNull($failed->fresh()->error_message);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $done->fresh()->status);
    }

    public function test_orphaned_pending_rows_are_queued(): void
    {
        Queue::fake();

        $old = $this->make(ContactService::MAIL_SEND_STATUS_PENDING);
        ContactMail::whereKey($old->id)->update(['created_at' => now()->subMinutes(10)]);
        $fresh = $this->make(ContactService::MAIL_SEND_STATUS_PENDING);

        $this->artisan('contact:resend-failed')->assertSuccessful();

        Queue::assertPushed(SendContactMailJob::class, 1);
        Queue::assertPushed(SendContactMailJob::class, fn ($job) => $job->contactMail->id === $old->id);
    }

    public function test_only_old_processing_rows_are_requeued(): void
    {
        Queue::fake();

        $recent = $this->make(ContactService::MAIL_SEND_STATUS_PROCESSING);
        $stuck = $this->make(ContactService::MAIL_SEND_STATUS_PROCESSING);
        ContactMail::whereKey($stuck->id)->update(['updated_at' => now()->subHour()]);

        $this->artisan('contact:resend-failed')->assertSuccessful();

        Queue::assertPushed(SendContactMailJob::class, 1);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_PROCESSING, $recent->fresh()->status);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_PENDING, $stuck->fresh()->status);
    }

    public function test_more_than_one_chunk_of_failed_rows_are_all_requeued(): void
    {
        Queue::fake();

        // 1,001 rows = one more than Laravel's default chunk size of 1,000. Offset paging would
        // skip the last row once the first 1,000 are reset to Pending.
        $now = now()->toDateTimeString();
        $rows = [];
        for ($i = 0; $i < 1001; $i++) {
            $rows[] = [
                'name' => 'A', 'email' => 'a@example.com', 'phone' => '1', 'subject' => 'S',
                'mail_message' => 'M', 'request_source' => '[]',
                'status' => ContactService::MAIL_SEND_STATUS_FAILED, 'error_message' => 'boom',
                'created_at' => $now, 'updated_at' => $now,
            ];
        }
        foreach (array_chunk($rows, 200) as $chunk) {
            ContactMail::insert($chunk);
        }

        $this->artisan('contact:resend-failed', ['--pending' => -1, '--stuck' => 0])
            ->expectsOutput('Re-queued 1001 contact mail(s).')
            ->assertSuccessful();

        Queue::assertPushed(SendContactMailJob::class, 1001);
        $this->assertSame(0, ContactMail::where('status', ContactService::MAIL_SEND_STATUS_FAILED)->count());
    }

    public function test_row_that_completes_after_selection_is_not_reset_or_requeued(): void
    {
        Queue::fake();

        $failed = $this->make(ContactService::MAIL_SEND_STATUS_FAILED);

        // Simulate a retry finishing between the SELECT and our UPDATE.
        ContactMail::retrieved(function (ContactMail $model) {
            ContactMail::whereKey($model->id)->update([
                'status' => ContactService::MAIL_SEND_STATUS_SUCCESS,
                'updated_at' => now()->addSecond(),
            ]);
        });

        $this->artisan('contact:resend-failed')
            ->expectsOutput('Re-queued 0 contact mail(s).')
            ->assertSuccessful();

        Queue::assertNothingPushed();
        $this->assertSame(ContactService::MAIL_SEND_STATUS_SUCCESS, $failed->fresh()->status);
    }

    public function test_pending_row_with_a_recently_queued_job_is_left_alone(): void
    {
        Queue::fake();

        // Workers are down: the job was queued 5 minutes ago and is simply waiting. Re-queuing it on every
        // scheduled run would add another job per waiting contact per run.
        $waiting = $this->make(ContactService::MAIL_SEND_STATUS_PENDING);
        ContactMail::whereKey($waiting->id)->update(['created_at' => now()->subMinutes(10), 'queued_at' => now()->subMinutes(5)]);

        $this->artisan('contact:resend-failed')->expectsOutput('Re-queued 0 contact mail(s).')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_running_the_command_repeatedly_queues_a_waiting_contact_only_once(): void
    {
        Queue::fake();

        // Saved but never queued (dispatch failed / process died): genuinely needs recovery.
        $orphan = $this->make(ContactService::MAIL_SEND_STATUS_PENDING);
        ContactMail::whereKey($orphan->id)->update(['created_at' => now()->subMinutes(10), 'queued_at' => null]);

        foreach (range(1, 5) as $run) { // five scheduler runs while no worker is processing the queue
            $this->artisan('contact:resend-failed')->assertSuccessful();
        }

        Queue::assertPushed(SendContactMailJob::class, 1);
        $this->assertNotNull($orphan->fresh()->queued_at);
    }

    public function test_pending_row_whose_job_is_old_enough_to_be_presumed_lost_is_recovered(): void
    {
        Queue::fake();

        $lost = $this->make(ContactService::MAIL_SEND_STATUS_PENDING);
        ContactMail::whereKey($lost->id)->update(['created_at' => now()->subHours(3), 'queued_at' => now()->subHours(2)]);

        $this->artisan('contact:resend-failed')->expectsOutput('Re-queued 1 contact mail(s).')->assertSuccessful();

        Queue::assertPushed(SendContactMailJob::class, 1);
        $this->assertTrue($lost->fresh()->queued_at->gt(now()->subMinute()));
    }

    public function test_failed_rows_are_retried_only_after_the_backoff_when_asked(): void
    {
        Queue::fake();

        $recent = $this->make(ContactService::MAIL_SEND_STATUS_FAILED);   // failed just now
        $old = $this->make(ContactService::MAIL_SEND_STATUS_FAILED);
        ContactMail::whereKey($old->id)->update(['updated_at' => now()->subMinutes(20)]);

        $this->artisan('contact:resend-failed', ['--failed-after' => 15])->assertSuccessful();

        Queue::assertPushed(SendContactMailJob::class, 1);
        Queue::assertPushed(SendContactMailJob::class, fn ($job) => $job->contactMail->id === $old->id);
        $this->assertSame(ContactService::MAIL_SEND_STATUS_FAILED, $recent->fresh()->status);
    }

    /** @return array<string, array{0: string, 1: int}> queue driver => status the row must have afterwards */
    public static function queueDrivers(): array
    {
        return [
            // database queue: reset + enqueue are one transaction, so the failed dispatch rolls everything back
            'database queue (atomic)' => ['database', ContactService::MAIL_SEND_STATUS_FAILED],
            // external queue: cannot join the transaction, so the marker is released but the reset stays
            'external queue (compensated)' => ['redis', ContactService::MAIL_SEND_STATUS_PENDING],
        ];
    }

    #[DataProvider('queueDrivers')]
    public function test_a_failed_dispatch_does_not_leave_the_row_marked_as_queued(string $driver, int $expectedStatus): void
    {
        Queue::fake();
        config(['queue.default' => $driver]);

        $failed = $this->make(ContactService::MAIL_SEND_STATUS_FAILED);
        ContactMail::whereKey($failed->id)->update(['created_at' => now()->subMinutes(30)]); // a real failure is not brand new

        $real = $this->app->make(Dispatcher::class);
        $this->instance(Dispatcher::class, \Mockery::mock(Dispatcher::class, function ($mock) {
            $mock->shouldReceive('dispatch')->andThrow(new \RuntimeException('queue unavailable'));
        }));

        try {
            $this->artisan('contact:resend-failed')->run();
            $this->fail('the dispatch failure must surface so the run is not reported as a success');
        } catch (\RuntimeException $e) {
            $this->assertSame('queue unavailable', $e->getMessage());
        }

        // No job exists, so the row must NOT look queued (otherwise it is skipped for 60 minutes).
        $row = $failed->fresh();
        $this->assertNull($row->queued_at);
        $this->assertSame($expectedStatus, $row->status);

        // The queue is back: the very next run recovers it.
        $this->instance(Dispatcher::class, $real);
        $this->artisan('contact:resend-failed')->expectsOutput('Re-queued 1 contact mail(s).')->assertSuccessful();
        Queue::assertPushed(SendContactMailJob::class, 1);
    }
}
