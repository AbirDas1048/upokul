<?php

namespace App\Jobs;

use App\Mail\ContactMailMailable;
use App\Mail\ContactThankYouMailable;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendContactMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    /**
     * A row left in Processing longer than this is treated as a crashed attempt
     * and may be claimed again (matches the `contact:resend-failed --stuck` default).
     */
    public const STALE_PROCESSING_MINUTES = 15;

    public ContactMail $contactMail;

    /**
     * Create a new job instance.
     */
    public function __construct(ContactMail $contactMail)
    {
        $this->contactMail = $contactMail;
    }

    /**
     * Single conditional UPDATE (check + write in one statement, so it cannot race):
     *   Pending (0) / Failed (2)               -> Processing (3)
     *   Processing (3) older than the stale window (crashed worker) -> Processing (3)
     * Completed (1) and a fresh Processing row are never claimed.
     */
    private function claim(): bool
    {
        return ContactMail::query()
            ->whereKey($this->contactMail->getKey())
            ->where(function ($q) {
                $q->whereIn('status', [
                    ContactService::MAIL_SEND_STATUS_PENDING,
                    ContactService::MAIL_SEND_STATUS_FAILED,
                ])->orWhere(function ($q) {
                    $q->where('status', ContactService::MAIL_SEND_STATUS_PROCESSING)
                        ->where('updated_at', '<', now()->subMinutes(self::STALE_PROCESSING_MINUTES));
                });
            })
            ->update([
                'status' => ContactService::MAIL_SEND_STATUS_PROCESSING,
                'updated_at' => now(),
            ]) === 1;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Atomic claim: exactly one copy of the job can move the row to Processing.
        // A copy that loses (already Completed, or another worker is Processing) stops
        // here, so the owner e-mail can never be sent twice.
        if (! $this->claim()) {
            return;
        }

        $this->contactMail->refresh();

        try {
            Mail::to(config('custom.office_email'))->send(new ContactMailMailable($this->contactMail));

            // Processing (3) -> Completed (1)
            $this->contactMail->update([
                'status' => ContactService::MAIL_SEND_STATUS_SUCCESS,
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            $this->contactMail->update([
                'status' => ContactService::MAIL_SEND_STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            throw $e; // required for retry
        }

        // Customer confirmation: failure must not fail the job (would re-send the owner mail on retry).
        try {
            Mail::to($this->contactMail->email)->send(new ContactThankYouMailable($this->contactMail));
        } catch (\Throwable $e) {
            Log::warning('Contact thank-you mail failed: '.$e->getMessage(), [
                'contact_mail_id' => $this->contactMail->id,
            ]);
        }
    }
}
