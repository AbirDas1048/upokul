<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Bot / abuse checks for the public contact form.
 *
 * Layers (cheapest first):
 *   1. Honeypot field      - invisible to people, bots fill every input.
 *   2. Signed form token   - issued when the page renders; proves the form was loaded,
 *                            cannot be forged (encrypted), expires, and must be "old enough".
 *   3. Optional Turnstile  - real bot challenge, enabled by setting the two keys in .env.
 *   4. Duplicate guard     - the same message from the same address is only accepted once.
 *
 * Request-rate limiting is done separately by the "contact" rate limiter
 * (AppServiceProvider) so it also covers requests that never reach this class.
 */
class ContactSpamGuard
{
    public const HONEYPOT_FIELD = 'contact_extra';

    public const TOKEN_FIELD = 'form_token';

    public const TURNSTILE_FIELD = 'cf-turnstile-response';

    public const VERDICT_HONEYPOT = 'honeypot';

    public const VERDICT_TOKEN = 'invalid_token';

    public const VERDICT_TOO_FAST = 'too_fast';

    public const VERDICT_CAPTCHA = 'captcha';

    /** Encrypted timestamp of when the form was rendered. */
    public static function issueToken(?CarbonInterface $at = null): string
    {
        return Crypt::encryptString((string) ($at ?? now())->getTimestamp());
    }

    public static function turnstileEnabled(): bool
    {
        return filled(config('services.turnstile.site_key')) && filled(config('services.turnstile.secret'));
    }

    /** True only for the silent-drop case; the caller must pretend it worked. */
    public function isHoneypotTripped(Request $request): bool
    {
        return filled($request->input(self::HONEYPOT_FIELD));
    }

    /**
     * Returns null when the submission looks human, otherwise a VERDICT_* constant.
     */
    public function verdict(Request $request): ?string
    {
        $age = $this->tokenAgeSeconds($request->input(self::TOKEN_FIELD));

        if ($age === null || $age > config('custom.contact.token_max_hours') * 3600) {
            return self::VERDICT_TOKEN;
        }

        if ($age < config('custom.contact.min_seconds')) {
            return self::VERDICT_TOO_FAST;
        }

        return $this->challengeFailed($request) ? self::VERDICT_CAPTCHA : null;
    }

    /**
     * Atomic "first one wins": Cache::add only succeeds if the key did not exist, so a
     * double-click, a retry loop or a replaying bot is accepted exactly once per window.
     * The caller must call forgetDuplicate() if it then fails to store the message, so an
     * honest retry is not mistaken for a duplicate of something that was never saved.
     */
    public function isDuplicate(string $email, string $message): bool
    {
        return ! Cache::add(
            $this->duplicateKey($email, $message),
            true,
            now()->addMinutes(max(1, (int) config('custom.contact.duplicate_minutes')))
        );
    }

    /** Releases the reservation made by isDuplicate() (used when persisting the message failed). */
    public function forgetDuplicate(string $email, string $message): void
    {
        Cache::forget($this->duplicateKey($email, $message));
    }

    private function duplicateKey(string $email, string $message): string
    {
        return 'contact:dup:'.hash('sha256', mb_strtolower(trim($email))."\n".preg_replace('/\s+/u', ' ', trim($message)));
    }

    private function challengeFailed(Request $request): bool
    {
        return self::turnstileEnabled() && ! $this->turnstilePasses($request);
    }

    private function tokenAgeSeconds(mixed $token): ?int
    {
        $issuedAt = $this->decryptToken($token);

        if ($issuedAt === null || ! ctype_digit($issuedAt)) {
            return null;
        }

        $age = now()->getTimestamp() - (int) $issuedAt;

        return $age >= 0 ? $age : null;
    }

    private function decryptToken(mixed $token): ?string
    {
        if (! is_string($token) || $token === '') {
            return null;
        }

        try {
            return Crypt::decryptString($token);
        } catch (DecryptException) {
            return null; // tampered / forged / from another app key
        }
    }

    private function turnstilePasses(Request $request): bool
    {
        $response = $request->input(self::TURNSTILE_FIELD);

        if (! is_string($response) || $response === '') {
            return false;
        }

        try {
            $result = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => $response,
                'remoteip' => $request->ip(),
            ]);

            return $result->successful() && $result->json('success') === true;
        } catch (\Throwable $e) {
            // Fail closed: if we cannot verify, we do not accept (and we say why in the log).
            Log::warning('Turnstile verification failed: '.$e->getMessage());

            return false;
        }
    }
}
