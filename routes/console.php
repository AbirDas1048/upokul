<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Safety net for contact e-mails: re-queues enquiries that are Failed, Pending with no job, or stuck in
// Processing. Needs the scheduler to run (cron: `* * * * * php artisan schedule:run`, or
// `php artisan schedule:work`; the Docker dev stack can run `docker compose exec app php artisan schedule:work`).
Schedule::command('contact:resend-failed', ['--failed-after' => 15])->everyFiveMinutes()->withoutOverlapping();
