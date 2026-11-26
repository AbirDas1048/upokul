<?php

namespace App\Console\Commands;

use App\Jobs\SendContactMailJob;
use App\Models\ContactMail;
use App\Services\ContactService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResendFailedContactMails extends Command
{
    protected $signature = 'contact:resend-failed
                            {--id= : Resend only this contact_mails ID}
                            {--pending=2 : Also queue Pending rows (status 0) older than N minutes that have no job (-1 to disable)}
                            {--requeue-after=60 : A Pending row whose job was queued less than N minutes ago is left alone (its job is presumed alive); never-queued rows are always eligible}
                            {--failed-after=0 : Only retry Failed rows whose last attempt was more than N minutes ago (the scheduler uses 15 so a permanent failure is not retried every run)}
                            {--stuck=15 : Also re-queue rows stuck in Processing for more than N minutes (0 to disable)}';

    protected $description = 'Queue contact mails that are Pending with no job (0), Failed (2) or stuck in Processing (3)';

    public function handle(): int
    {
        $requeueAfter = max(0, (int) $this->option('requeue-after'));
        $failedAfter = max(0, (int) $this->option('failed-after'));

        $query = ContactMail::query()->where(function ($q) use ($failedAfter, $requeueAfter) {
            $q->where(function ($q) use ($failedAfter) {
                $q->where('status', ContactService::MAIL_SEND_STATUS_FAILED)
                    ->when($failedAfter > 0, fn ($q) => $q->where('updated_at', '<=', now()->subMinutes($failedAfter)));
            });

            // Pending rows with no live job. `queued_at` makes this idempotent: a row whose job was queued
            // recently is NOT queued again (otherwise every scheduled run would add another job per waiting
            // contact while workers are down); a row that was never queued (NULL: the dispatch failed or
            // the process died first) or whose job is old enough to be presumed lost is recovered.
            $pending = (int) $this->option('pending');
            if ($pending >= 0) {
                $q->orWhere(function ($q) use ($pending, $requeueAfter) {
                    $q->where('status', ContactService::MAIL_SEND_STATUS_PENDING)
                        ->where('created_at', '<=', now()->subMinutes($pending))
                        ->where(function ($q) use ($requeueAfter) {
                            $q->whereNull('queued_at')
                                ->orWhere('queued_at', '<=', now()->subMinutes($requeueAfter));
                        });
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
        // With the `database` queue the job row lives in the same database, so reset + enqueue are one
        // transaction (a failed dispatch rolls the reset back too). Other queues cannot join it, so
        // requeue() compensates instead.
        $atomic = config('queue.connections.'.config('queue.default').'.driver') === 'database';

        $query->eachById(function (ContactMail $contactMail) use (&$count, $atomic) {
            $queued = $atomic
                ? DB::transaction(fn () => $this->requeue($contactMail))
                : $this->requeue($contactMail);

            if ($queued) {
                $count++;
            }
        });

        $this->info("Re-queued {$count} contact mail(s).");

        return self::SUCCESS;
    }

    /**
     * Resets the row and queues its job. Returns false when nothing was queued.
     *
     * The reset only applies if the row is still exactly as it was when selected: if a retry finished in
     * the meantime (Completed) or another worker touched it, 0 rows match and we neither overwrite that
     * newer state nor queue a duplicate. `queued_at` is set in the same statement so the next scheduled
     * run does not queue it again; if the dispatch then fails there is no job, so the marker is released
     * (only if nobody changed it since) and the row stays eligible for the next run.
     */
    private function requeue(ContactMail $contactMail): bool
    {
        $markedAt = now();

        $reset = ContactMail::query()
            ->whereKey($contactMail->getKey())
            ->where('status', $contactMail->status)
            ->where('updated_at', $contactMail->updated_at)
            ->update([
                'status' => ContactService::MAIL_SEND_STATUS_PENDING,
                'error_message' => null,
                'queued_at' => $markedAt,
                'updated_at' => $markedAt,
            ]);

        if ($reset !== 1) {
            return false;
        }

        try {
            SendContactMailJob::dispatch($contactMail->refresh());
        } catch (\Throwable $e) {
            ContactMail::query()
                ->whereKey($contactMail->getKey())
                ->where('queued_at', $markedAt)
                ->update(['queued_at' => null]);

            throw $e;
        }

        return true;
    }
}
