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

    /** A live attempt is killed by the worker after this many seconds. */
    public int $timeout = 60;

    /**
     * A row is "abandoned" (its attempt is dead) only once it has sat in Processing this long.
     * Chosen to sit strictly between the two timings that matter:
     *
     *     timeout (60s)  <  STALE (75s)  <  queue retry_after / redelivery (90s)
     *
     *  - A LIVE attempt always finishes or is killed within 60s, so its claim is always younger than
     *    75s: no other job (a retry of an older message, the recovery command's new message, a
     *    duplicate...) can take it over, which is what prevents a double send.
     *  - A CRASHED attempt's claim is already older than 75s when the queue redelivers the message at
     *    90s, so the redelivery recovers it automatically and the e-mail is still sent.
     * (JobTest asserts this ordering against config('queue').)
     */
    public const STALE_PROCESSING_SECONDS = 75;

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
     *   Processing (3)            -> Processing (3) only when the claim is provably abandoned, i.e. it
     *                                is older than STALE_PROCESSING_SECONDS (see the constant for why
     *                                that is safe). It deliberately does NOT depend on attempts():
     *                                that counts the queue message's tries, not the age or owner of
     *                                the current claim, so a retry of one message could otherwise
     *                                take over a live claim made by a different message.
     * Completed (1) and any live (fresh) Processing row are never claimed.
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
                        ->where('updated_at', '<', now()->subSeconds(self::STALE_PROCESSING_SECONDS));
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
