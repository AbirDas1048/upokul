<?php

namespace App\Http\Middleware;

use App\Support\Installer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Before installation every page leads to the wizard; afterwards the wizard is gone (404), so nobody
 * can re-run it and create another master admin.
 *
 * It runs before the session starts: with the default `database` session driver the sessions table
 * does not exist yet, so until installation finishes sessions and cache fall back to files.
 */
class InstallationGuard
{
    public function __construct(private readonly Installer $installer) {}

    public function handle(Request $request, Closure $next): Response
    {
        $isWizard = $request->is('install', 'install/*');

        if ($this->installer->installed()) {
            abort_if($isWizard, 404);

            return $next($request);
        }

        // Only the database-backed stores need a migrated schema; anything else is left alone.
        if (config('session.driver') === 'database') {
            config(['session.driver' => 'file']);
        }

        if (config('cache.default') === 'database') {
            config(['cache.default' => 'file']);
        }

        if (! $isWizard) {
            return redirect()->route('install.index');
        }

        return $next($request);
    }
}
