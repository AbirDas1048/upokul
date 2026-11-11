<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * "contact" limiter used by POST /contact. Several buckets are checked at once and the first
     * one that is full answers with HTTP 429 + Retry-After.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('contact', function (Request $request) {
            $config = config('custom.contact');
            $ip = (string) $request->ip();
            $email = Str::lower(trim((string) $request->input('email')));

            $limits = [
                Limit::perMinute($config['per_minute'])->by('contact:ip-minute:'.$ip),
                Limit::perHour($config['per_hour'])->by('contact:ip-hour:'.$ip),
                Limit::perHour($config['global_per_hour'])->by('contact:global'),
            ];

            // Per address (hashed, so no e-mail is stored in the cache key): stops the site from
            // being used to mail-bomb one victim with "thank you" messages from many IPs.
            if ($email !== '' && mb_strlen($email) <= 254) {
                $limits[] = Limit::perHour($config['per_email_hour'])->by('contact:email:'.sha1($email));
            }

            return array_map(
                fn (Limit $limit) => $limit->response(fn (Request $request, array $headers) => response()->json([
                    'success' => false,
                    'status' => 429,
                    'message' => 'Too Many Requests',
                    'data' => ['form' => ['You are sending messages too quickly. Please wait a few minutes and try again.']],
                ], 429, $headers)),
                $limits
            );
        });
    }
}
