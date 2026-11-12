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
     * Kill a hung attempt well before the queue redelivers the job (retry_after defaults to 90s), so a
     * redelivery always means the previous attempt is gone and may safely be taken over.
     */
    public int $timeout = 60;

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
     *   Pending (0) / Failed (2)  -> Processing (3)
     *   Processing (3)            -> Processing (3) only if the previous attempt is dead:
     *                                 - this is a REDELIVERY (attempts() > 1: the queue only redelivers
     *                                   after the earlier attempt crashed or hit its timeout), or
     *                                 - the row has sat in Processing longer than the stale window
     *                                   (picked up by `contact:resend-failed`).
     * Completed (1) and another worker's fresh Processing row are never claimed.
     */
    private function claim(): bool
    {
        $isRedelivery = $this->attempts() > 1;

        return ContactMail::query()
            ->whereKey($this->contactMail->getKey())
            ->where(function ($q) use ($isRedelivery) {
                $q->whereIn('status', [
                    ContactService::MAIL_SEND_STATUS_PENDING,
                    ContactService::MAIL_SEND_STATUS_FAILED,
                ])->orWhere(function ($q) use ($isRedelivery) {
                    $q->where('status', ContactService::MAIL_SEND_STATUS_PROCESSING)
                        ->when(! $isRedelivery, fn ($q) => $q->where('updated_at', '<', now()->subMinutes(self::STALE_PROCESSING_MINUTES)));
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

        try {
            // Inside the try: if this read fails after the claim, the row must not stay in
            // Processing (the retry could not claim it and the job would be deleted unsent).
            $this->contactMail->refresh();

            Mail::to(config('custom.office_email'))->send(new ContactMailMailable($this->contactMail));

            // Processing (3) -> Completed (1)
            $this->contactMail->update([
                'status' => ContactService::MAIL_SEND_STATUS_SUCCESS,
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            // Query-builder update by key: it does not depend on the in-memory model state
            // (which may be stale or unrefreshed), so the row always lands in Failed (retryable).
            ContactMail::query()->whereKey($this->contactMail->getKey())->update([
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
