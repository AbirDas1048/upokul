<?php

namespace App\Support;

use App\Models\ContentItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * First-time installation: requirement checks, migrations, the owner/site settings and the master admin.
 *
 * "Installed" is remembered in two places: a lock file (cheap, no database needed to check) and the
 * `installed_at` setting. Either one is enough, so a re-created container with a persistent database
 * is not sent back through the wizard.
 */
class Installer
{
    public const WELCOME_KEY = 'install.welcome_token';

    private ?bool $ran = null;

    public function __construct(private readonly Settings $settings) {}

    /** The pending popup ticket, or null. */
    public function welcomeTicket(): ?string
    {
        $token = (string) $this->settings->get(self::WELCOME_KEY, '');

        return $token === '' ? null : $token;
    }

    /**
     * True exactly once: when the visitor arrives with the ticket the installer handed out. The ticket is
     * deleted on the spot, so a refresh, a bookmark or anybody else never sees the popup.
     */
    public function consumeWelcome(mixed $presented): bool
    {
        $ticket = $this->welcomeTicket();

        if ($ticket === null || ! is_string($presented) || ! hash_equals($ticket, $presented)) {
            return false;
        }

        $this->settings->set([self::WELCOME_KEY => null]);

        return true;
    }

    /** Forget the remembered answer (tests; or after the lock file / marker was changed by hand). */
    public function forget(): void
    {
        $this->ran = null;
    }

    /** Whether the application should behave as installed (config can force it, e.g. in tests). */
    public function installed(): bool
    {
        $forced = config('installer.installed');

        return $forced !== null ? (bool) $forced : $this->hasRun();
    }

    /** Whether the wizard has really been completed (lock file or database marker). */
    public function hasRun(): bool
    {
        if ($this->ran !== null) {
            return $this->ran;
        }

        $lock = (string) config('installer.lock');

        if (is_file($lock)) {
            return $this->ran = true;
        }

        try {
            $ran = $this->settings->has('installed_at');
        } catch (\Throwable) {
            $ran = false;
        }

        if ($ran) {
            $this->writeLock(); // self-heal a lost lock file
        }

        return $this->ran = $ran;
    }

    /** @return list<array{label: string, ok: bool, detail: string}> */
    public function requirements(): array
    {
        $config = config('installer.requirements');
        $rows = [[
            'label' => 'PHP '.$config['php'].' or newer',
            'ok' => version_compare(PHP_VERSION, $config['php'], '>='),
            'detail' => 'Running '.PHP_VERSION,
        ]];

        foreach ($config['extensions'] as $extension) {
            $rows[] = ['label' => "PHP extension: {$extension}", 'ok' => extension_loaded($extension), 'detail' => extension_loaded($extension) ? 'Loaded' : 'Missing'];
        }

        foreach ($config['writable'] as $path) {
            $full = $path === 'public/uploads' ? (string) config('installer.upload_path') : base_path($path);
            File::ensureDirectoryExists($full);
            $ok = is_writable($full);
            $rows[] = ['label' => "Writable: {$path}", 'ok' => $ok, 'detail' => $ok ? 'Writable' : 'Not writable'];
        }

        return $rows;
    }

    public function requirementsMet(): bool
    {
        return collect($this->requirements())->every(fn (array $row) => $row['ok']);
    }

    /**
     * Run the whole installation. Everything database-related happens in one place so a failure
     * leaves the site un-installed (the wizard simply shows the error and can be retried).
     *
     * @param  array{app_name: string, tagline?: ?string, owner_name: string, owner_designation?: ?string, owner_email: string, owner_phone?: ?string, office_address?: ?string, office_phone?: ?string, office_email: string, whatsapp_number?: ?string, home_theme: string, logo?: ?UploadedFile, admin_name: string, admin_email: string, admin_password: string}  $data
     */
    public function install(array $data): User
    {
        $this->prepareDatabase();
        Artisan::call('migrate', ['--force' => true]);

        // The customer's own logo (optional). Without one the neutral placeholder logo is used.
        $logo = ! empty($data['logo']) ? Uploads::store($data['logo']) : null;

        try {
            $admin = DB::transaction(function () use ($data, $logo) {
                $this->settings->set([
                    'branding.logo' => $logo,
                    'general.app_name' => $data['app_name'],
                    'general.tagline' => $data['tagline'] ?? null,
                    'general.owner_name' => $data['owner_name'],
                    'general.owner_designation' => $data['owner_designation'] ?? null,
                    'general.owner_email' => $data['owner_email'],
                    'general.owner_phone' => $data['owner_phone'] ?? null,
                    'contact.office_address' => $data['office_address'] ?? null,
                    'contact.office_phone' => $data['office_phone'] ?? null,
                    'contact.office_email' => $data['office_email'],
                    'contact.whatsapp_number' => $data['whatsapp_number'] ?? null,
                    'appearance.home_theme' => $data['home_theme'],
                ]);

                // Always: the website must never be blank after installing.
                foreach (array_keys(config('content_types')) as $type) {
                    ContentItem::seedDefaults($type);
                }

                foreach (config('permissions.starter_roles') as $name => $def) {
                    $role = Role::query()->firstOrCreate(['name' => $name], ['description' => $def['description']]);
                    $role->syncPermissions($def['permissions']);
                }

                $admin = new User;
                $admin->name = $data['admin_name'];
                $admin->email = $data['admin_email'];
                $admin->password = $data['admin_password'];
                $admin->is_master = true;
                $admin->is_active = true;
                $admin->email_verified_at = now();
                $admin->save();

                $this->settings->set(['installed_at' => now()->toIso8601String()]);

                // Go live with the standard landing page plus the details entered in the wizard.
                app(Publisher::class)->publish();

                // One-time ticket for the "installation complete" popup on the website (see consumeWelcome()).
                $this->settings->set([self::WELCOME_KEY => Str::random(40)]);

                return $admin;
            });
        } catch (\Throwable $e) {
            Uploads::delete($logo); // do not leave an orphan upload behind a failed installation

            throw $e;
        }

        // APP_NAME in .env is best-effort: the database value above is what the site actually uses.
        try {
            (new EnvWriter((string) config('installer.env_file')))->set(['APP_NAME' => $data['app_name']]);
        } catch (\Throwable $e) {
            report($e);
        }

        $this->writeLock();
        $this->ran = true;

        return $admin;
    }

    /** SQLite will not create its own file; every other driver needs the schema to exist already. */
    private function prepareDatabase(): void
    {
        $connection = config('database.default');

        if (config("database.connections.{$connection}.driver") !== 'sqlite') {
            return;
        }

        $path = (string) config("database.connections.{$connection}.database");

        if ($path !== ':memory:' && $path !== '' && ! is_file($path)) {
            File::ensureDirectoryExists(dirname($path));
            touch($path);
        }
    }

    private function writeLock(): void
    {
        $lock = (string) config('installer.lock');

        try {
            File::ensureDirectoryExists(dirname($lock));
            File::put($lock, 'Installed '.now()->toIso8601String()."\n");
        } catch (\Throwable) {
            // The database marker still records the installation.
        }
    }
}
