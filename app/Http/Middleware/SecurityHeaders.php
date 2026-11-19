<?php

namespace App\Http\Middleware;

use App\Services\ContactSpamGuard;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Browser-side hardening applied to every web response:
 *  - Content-Security-Policy  : only our own scripts/styles/fonts run; a per-request nonce
 *                               allows the single inline script. Blocks injected scripts (XSS),
 *                               foreign form targets, plugins and clickjacking frames.
 *  - X-Content-Type-Options   : no MIME sniffing (JSON can never be "reinterpreted" as HTML).
 *  - X-Frame-Options / frame-ancestors : cannot be embedded by another site (clickjacking).
 *  - Referrer-Policy / Permissions-Policy : no URL leakage, no camera/mic/geolocation.
 *  - Strict-Transport-Security: only sent over HTTPS.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Must run before the view renders so @vite tags and the inline script share the nonce.
        $nonce = Vite::useCspNonce();

        /** @var Response $response */
        $response = $next($request);

        $headers = $response->headers;

        $headers->set('Content-Security-Policy', $this->policy($nonce, $request->isSecure()));
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $headers->remove('X-Powered-By');

        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function policy(string $nonce, bool $secure): string
    {
        $dev = $this->viteDevOrigins();
        $turnstile = ContactSpamGuard::turnstileEnabled() ? ' https://challenges.cloudflare.com' : '';

        $directives = [
            "default-src 'self'",
            // Inline <script> only with this request's nonce; no 'unsafe-inline', no 'unsafe-eval'.
            "script-src 'self' 'nonce-{$nonce}'{$turnstile}{$dev['http']}",
            // Inline style="" attributes are used for reveal delays / hero backgrounds.
            "style-src 'self' 'unsafe-inline'{$dev['http']}",
            "img-src 'self' data:{$dev['http']}",
            "font-src 'self' data:{$dev['http']}",
            "media-src 'self'",
            "connect-src 'self'{$turnstile}{$dev['http']}{$dev['ws']}",
            // Google Maps embed on the contact section (+ Turnstile widget when enabled).
            "frame-src https://www.google.com{$turnstile}",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        if ($secure) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }

    /**
     * While `npm run dev` is running Laravel writes public/hot; allow that one origin (and its
     * HMR websocket) so development keeps working. Never present in production builds.
     *
     * @return array{http: string, ws: string}
     */
    private function viteDevOrigins(): array
    {
        $origin = $this->hotOrigin();

        return $origin === null
            ? ['http' => '', 'ws' => '']
            : ['http' => ' '.$origin, 'ws' => ' '.preg_replace('~^http~', 'ws', $origin)];
    }

    /** The dev-server origin from public/hot, or null when absent / not a plain http(s) origin. */
    private function hotOrigin(): ?string
    {
        $hot = public_path('hot');
        $origin = is_file($hot) ? trim((string) file_get_contents($hot)) : '';

        return preg_match('~^https?://[A-Za-z0-9.\-]+(:\d+)?$~', $origin) === 1 ? $origin : null;
    }
}
