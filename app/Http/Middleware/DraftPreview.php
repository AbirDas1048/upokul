<?php

namespace App\Http\Middleware;

use App\Support\Settings;
use App\Support\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * `?preview=draft` shows a signed-in admin the website with their unpublished changes (this is what the
 * admin "Live preview" frame uses). Everyone else — visitors and signed-out users — always gets the published
 * site, whatever the query string says. Runs after StartSession, so the admin's session is available.
 */
class DraftPreview
{
    public function handle(Request $request, Closure $next): Response
    {
        $draft = $request->isMethod('GET') && $request->query('preview') === 'draft' && $request->user()?->is_active;

        if ($draft) {
            app(Site::class)->useDraft(true);
            app(Settings::class)->applyToConfig(draft: true);
        }

        try {
            /** @var Response $response */
            $response = $next($request);
        } finally {
            // Never leak draft state into the next request in a long-lived process (Octane, tests).
            if ($draft) {
                app(Site::class)->useDraft(false);
                app(Settings::class)->applyToConfig();
            }
        }

        if ($draft) {
            $response->headers->set('Cache-Control', 'no-store, private');
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
