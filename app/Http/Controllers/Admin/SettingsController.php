<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesSettingsForm;
use App\Http\Controllers\Controller;
use App\Support\Activity;
use App\Support\EnvWriter;
use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/** General, contact, branding, SEO and mail settings (one screen per group in config/site_settings.php). */
class SettingsController extends Controller
{
    use SavesSettingsForm;

    public function edit(Request $request, ?string $group = null): View|RedirectResponse
    {
        $groups = $this->visibleGroups();
        abort_if($groups === [], 403);

        // /admin/settings → the first group this admin may open.
        if ($group === null) {
            return redirect()->route('admin.settings.edit', array_key_first($groups));
        }

        abort_unless(isset($groups[$group]), 403);

        return view('admin.settings', [
            'groups' => $groups,
            'group' => $group,
            'def' => $groups[$group],
            'canEdit' => Gate::allows($groups[$group]['permission'].'.update'),
            'values' => $this->values($groups[$group]['fields'], $group),
        ]);
    }

    public function update(Request $request, string $group, Settings $settings): RedirectResponse
    {
        $def = config("site_settings.groups.{$group}");
        abort_if($def === null || $group === 'appearance', 404);
        Gate::authorize($def['permission'].'.update');

        $keys = $this->saveFields($request, $def['fields'], "{$group}.");
        Activity::log("settings.{$group}", "Updated {$def['label']} settings");

        if ($group === 'general') {
            $this->syncAppName((string) $settings->get('general.app_name'));
        }

        if ($group === 'mail') {
            $this->restartQueueWorkers();
        }

        return redirect()->route('admin.settings.edit', $group)->with('status', "{$def['label']} settings saved.");
    }

    /** Sends one message through the saved SMTP settings so a wrong password shows up immediately. */
    public function testMail(Request $request): RedirectResponse
    {
        $to = $request->user()->email;

        try {
            Mail::raw('This is a test message from '.config('app.name').'. Your mail settings work.', function ($message) use ($to) {
                $message->to($to)->subject('Test email from '.config('app.name'));
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Sending failed: '.$e->getMessage());
        }

        Activity::log('settings.mail', "Sent a test email to {$to}");

        return back()->with('status', "Test email sent to {$to}.");
    }

    /** @return array<string, array<string, mixed>> groups the current admin may open, in display order */
    private function visibleGroups(): array
    {
        return collect(config('site_settings.groups'))
            ->except('appearance')
            ->filter(fn ($def) => Gate::allows($def['permission'].'.view'))
            ->all();
    }

    /** @return array<string, mixed> current value per field name (secrets are never sent to the page) */
    private function values(array $fields, string $group): array
    {
        $settings = app(Settings::class);
        $values = [];

        foreach ($fields as $field) {
            $values[$field['name']] = $field['type'] === 'password' ? '' : $settings->get("{$group}.{$field['name']}");
        }

        return $values;
    }

    /** APP_NAME in .env follows the site name; the database value is authoritative either way. */
    private function syncAppName(string $name): void
    {
        try {
            (new EnvWriter((string) config('installer.env_file')))->set(['APP_NAME' => $name]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Long-running queue workers keep the old mail config in memory until they restart. */
    private function restartQueueWorkers(): void
    {
        try {
            Artisan::call('queue:restart');
        } catch (\Throwable) {
            //
        }
    }
}
