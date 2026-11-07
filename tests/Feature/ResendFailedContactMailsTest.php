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
}
