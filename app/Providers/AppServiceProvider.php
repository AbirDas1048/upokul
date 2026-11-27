<?php

namespace App\Providers;

use App\Models\PermissionRegistry;
use App\Models\User;
use App\Services\ContactService;
use App\Support\Installer;
use App\Support\Publisher;
use App\Support\Settings;
use App\Support\Site;
use App\Support\ViteDevServer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Settings::class);
        $this->app->singleton(Installer::class);
        $this->app->singleton(Site::class);
        $this->app->singleton(Publisher::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureTrustedProxies();
        $this->configureRateLimiting();
        $this->configureAdmin();
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

    /**
     * Admin panel wiring: site settings override config, every page sees `$site`, and each
     * "module.action" permission becomes a Gate ability (the master admin passes all of them).
     */
    private function configureAdmin(): void
    {
        if ($this->app->make(Installer::class)->installed()) {
            $this->app->make(Settings::class)->applyToConfig();
        }

        // A stale public/hot (npm run dev stopped) would make every page load its CSS/JS from a dead server.
        if (ViteDevServer::isStale()) {
            Vite::useHotFile(storage_path('framework/vite.hot.ignored'));
        }

        View::share('site', $this->app->make(Site::class));

        Gate::before(fn (User $user) => $user->isMaster() && $user->is_active ? true : null);

        foreach (PermissionRegistry::all() as $key) {
            Gate::define($key, fn (User $user) => $user->hasPermission($key));
        }

        RateLimiter::for('admin-login', fn (Request $request) => Limit::perMinute(5)->by(
            mb_strtolower((string) $request->input('email')).'|'.$request->ip()
        ));
    }
}
