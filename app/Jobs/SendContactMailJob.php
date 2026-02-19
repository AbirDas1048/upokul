<?php

namespace App\Jobs;

use App\Mail\ContactMailMailable;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendContactMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff  = 10;
    public ContactMail $contactMail;

    /**
     * Create a new job instance.
     */
    public function __construct( ContactMail $contactMail)
    {
        $this->contactMail = $contactMail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to(config('custom.office_email'))->send(new contactMailMailable($this->contactMail));

            $this->contactMail->update([
               'status' => ContactService::MAIL_SEND_STATUS_SUCCESS,
                'sent_at' => now()
            ]);
        }catch (\Throwable $e) {
            $this->contactMail->update([
                'status' => ContactService::MAIL_SEND_STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            throw $e; // required for retry
        }
    }
}
