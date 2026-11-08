<?php

namespace App\Console\Commands;

use App\Jobs\SendContactMailJob;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Console\Command;

class ResendFailedContactMails extends Command
{
    protected $signature = 'contact:resend-failed
                            {--id= : Resend only this contact_mails ID}
                            {--pending=2 : Also queue Pending rows (status 0) older than N minutes that have no job (-1 to disable)}
                            {--stuck=15 : Also re-queue rows stuck in Processing for more than N minutes (0 to disable)}';

    protected $description = 'Queue contact mails that are Pending with no job (0), Failed (2) or stuck in Processing (3)';

    public function handle(): int
    {
        $query = ContactMail::query()->where(function ($q) {
            $q->where('status', ContactService::MAIL_SEND_STATUS_FAILED);

            // Orphaned Pending rows: saved but no job ever queued for them.
            $pending = (int) $this->option('pending');
            if ($pending >= 0) {
                $q->orWhere(function ($q) use ($pending) {
                    $q->where('status', ContactService::MAIL_SEND_STATUS_PENDING)
                        ->where('created_at', '<=', now()->subMinutes($pending));
                });
            }

            $stuck = (int) $this->option('stuck');
            if ($stuck > 0) {
                $q->orWhere(function ($q) use ($stuck) {
                    $q->where('status', ContactService::MAIL_SEND_STATUS_PROCESSING)
                        ->where('updated_at', '<', now()->subMinutes($stuck));
                });
            }
        });

        if ($this->option('id')) {
            $query->whereKey($this->option('id'));
        }

        $count = 0;

        // eachById (keyset paging): the callback changes the status, which removes rows
        // from this result set, so offset paging (each) would skip later rows.
        $query->eachById(function (ContactMail $contactMail) use (&$count) {
            // Reset only if the row is still exactly as it was when selected. If a retry
            // finished in the meantime (Completed) or another worker touched it, 0 rows
            // match and we neither overwrite that newer state nor queue a duplicate.
            $reset = ContactMail::query()
                ->whereKey($contactMail->getKey())
                ->where('status', $contactMail->status)
                ->where('updated_at', $contactMail->updated_at)
                ->update([
                    'status' => ContactService::MAIL_SEND_STATUS_PENDING,
                    'error_message' => null,
                    'updated_at' => now(),
                ]);

            if ($reset === 1) {
                SendContactMailJob::dispatch($contactMail->refresh());
                $count++;
            }
        });

        $this->info("Re-queued {$count} contact mail(s).");

        return self::SUCCESS;
    }
}
