<?php

namespace App\Support;

/**
 * `npm run dev` writes public/hot; Laravel then loads every CSS/JS file from that dev server. When the dev
 * server has stopped (or is unreachable from inside Docker) the file is left behind and every page comes out
 * unstyled. This class tells whether the dev server really answers, so a stale hot file can be ignored and
 * the built assets (public/build) used instead.
 */
class ViteDevServer
{
    private static ?bool $reachable = null;

    /** Forget the remembered answer (tests; long-running workers). */
    public static function reset(): void
    {
        self::$reachable = null;
    }

    /** The dev-server origin written to public/hot, or null when absent / not a plain http(s) origin. */
    public static function origin(): ?string
    {
        $hot = public_path('hot');
        $origin = is_file($hot) ? trim((string) file_get_contents($hot)) : '';

        return preg_match('~^https?://[A-Za-z0-9.\-]+(:\d+)?$~', $origin) === 1 ? $origin : null;
    }

    /** True only when a hot file exists AND something is listening there. */
    public static function reachable(): bool
    {
        if (self::$reachable !== null) {
            return self::$reachable;
        }

        $origin = self::origin();

        if ($origin === null) {
            return self::$reachable = false;
        }

        $parts = parse_url($origin);
        $port = $parts['port'] ?? (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);
        $socket = @fsockopen((string) $parts['host'], (int) $port, $errno, $error, 0.2);

        if ($socket === false) {
            return self::$reachable = false;
        }

        fclose($socket);

        return self::$reachable = true;
    }

    /** Whether a hot file is present but nothing is serving it. */
    public static function isStale(): bool
    {
        return is_file(public_path('hot')) && ! self::reachable();
    }
}
