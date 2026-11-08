<?php

namespace Tests\Feature;

use App\Jobs\SendContactMailJob;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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
}
