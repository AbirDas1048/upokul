<?php

namespace App\Providers;

use App\Services\ContactService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        $this->configureTrustedProxies();
        $this->configureRateLimiting();
    }

    /**
     * Behind Docker / nginx / Cloudflare the real visitor IP arrives in X-Forwarded-For. It is only
     * believed from the proxies listed in TRUSTED_PROXIES (comma separated IPs/CIDRs, or * for all);
     * trusting nobody (the default) means a visitor cannot spoof their IP to dodge the rate limits.
     *
     * Read through config() and applied here, after configuration has loaded: calling env() while
     * the middleware stack is being built (or when `config:cache` is in use) returns nothing.
     */
    private function configureTrustedProxies(): void
    {
        $proxies = trim((string) config('custom.trusted_proxies'));

        if ($proxies !== '') {
            TrustProxies::at($proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }
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

            $limits = [
                Limit::perMinute($config['per_minute'])->by('contact:ip-minute:'.$ip),
                Limit::perHour($config['per_hour'])->by('contact:ip-hour:'.$ip),
                Limit::perHour($config['global_per_hour'])->by('contact:global'),
            ];

            // Per address: stops the site from being used to mail-bomb one victim with "thank you"
            // messages from many IPs. The address is canonicalised exactly like the contact service
            // does (control characters removed, lower-cased) so padding tricks cannot dodge the
            // bucket; non-string input (email[]=...) simply gets no e-mail bucket and is rejected
            // later by validation. SHA-256 keeps the e-mail out of the cache key.
            $email = ContactService::canonicalEmail($request->input('email'));

            if ($email !== '') {
                $limits[] = Limit::perHour($config['per_email_hour'])->by('contact:email:'.hash('sha256', $email));
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
