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

    public ContactMail $contactMail;

    /**
     * Create a new job instance.
     */
    public function __construct(ContactMail $contactMail)
    {
        $this->contactMail = $contactMail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Already completed (e.g. duplicate dispatch): never send twice.
        if ($this->contactMail->fresh()?->status === ContactService::MAIL_SEND_STATUS_SUCCESS) {
            return;
        }

        // Pending (0) / Failed (2, on retry) -> Processing (3)
        $this->contactMail->update([
            'status' => ContactService::MAIL_SEND_STATUS_PROCESSING,
        ]);

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
