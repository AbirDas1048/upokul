<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Key/value site settings stored in the `settings` table (one query per request, cached).
 * Keys are "<group>.<name>" or "section.<id>.<name>"; the schema lives in config/site_settings.php.
 */
class Settings
{
    private const CACHE_KEY = 'site.settings';

    /** @var array<string, string|null>|null */
    private ?array $rows = null;

    private ?array $snapshot = null;

    private bool $snapshotLoaded = false;

    /** @return array<string, string|null> raw stored values (encrypted ones still encrypted) */
    private function rows(): array
    {
        if ($this->rows !== null) {
            return $this->rows;
        }

        try {
            return $this->rows = Cache::rememberForever(self::CACHE_KEY, fn () => Setting::query()->pluck('value', 'key')->all());
        } catch (\Throwable) {
            // Before the first migration there is no table (or cache) yet.
            return $this->rows = [];
        }
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->rows());
    }

    /** Stored value; blank (null or '') falls back to the schema default, then to $default. */
    public function get(string $key, ?string $default = null): ?string
    {
        $value = $this->rows()[$key] ?? null;

        if ($value !== null && $value !== '' && $this->isEncrypted($key)) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Throwable) {
                $value = null; // APP_KEY changed: treat as not set
            }
        }

        return $this->resolve($key, $value, $default);
    }

    /** Blank (null or '') falls back to the schema default; an allow_blank field may stay empty. */
    private function resolve(string $key, ?string $value, ?string $default): ?string
    {
        if ($value === null || $value === '') {
            $field = $this->field($key);

            // A deliberately emptied field (allow_blank) stays empty instead of reverting to its default.
            if ($value === '' && ! empty($field['allow_blank'])) {
                return '';
            }

            return $field && array_key_exists('default', $field) ? (string) $field['default'] : $default;
        }

        return $value;
    }

    /**
     * The last PUBLISHED version of the website's settings and content (see Publisher), or null when
     * nothing has been published yet — then the public site shows the standard landing page.
     *
     * @return array{settings: array<string, string|null>, items: array<string, list<array>>}|null
     */
    public function snapshot(): ?array
    {
        if ($this->snapshotLoaded) {
            return $this->snapshot;
        }

        $this->snapshotLoaded = true;
        $decoded = json_decode((string) ($this->rows()[Publisher::KEY] ?? ''), true);

        return $this->snapshot = is_array($decoded) && isset($decoded['settings'], $decoded['items']) ? $decoded : null;
    }

    public function hasPublished(string $key): bool
    {
        return array_key_exists($key, $this->snapshot()['settings'] ?? []);
    }

    /** Like get(), but reads the published snapshot, never a draft. */
    public function published(string $key, ?string $default = null): ?string
    {
        return $this->resolve($key, $this->snapshot()['settings'][$key] ?? null, $default);
    }

    public function publishedBool(string $key, bool $default = true): bool
    {
        $value = $this->snapshot()['settings'][$key] ?? null;

        return $value === null ? $default : $value === '1';
    }

    public function bool(string $key, bool $default = true): bool
    {
        $value = $this->rows()[$key] ?? null;

        return $value === null ? $default : $value === '1';
    }

    /** @param  array<string, string|int|bool|null>  $pairs */
    public function set(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            $value = $value === null ? null : (string) $value;

            if ($value !== null && $value !== '' && $this->isEncrypted($key)) {
                $value = Crypt::encryptString($value);
            }

            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->flush();
    }

    public function flush(): void
    {
        $this->rows = null;
        $this->snapshot = null;
        $this->snapshotLoaded = false;

        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable) {
            //
        }
    }

    public function isEncrypted(string $key): bool
    {
        return in_array($key, config('site_settings.encrypted', []), true);
    }

    /** @return array<string, mixed>|null the schema entry for a setting key */
    public function field(string $key): ?array
    {
        if (str_starts_with($key, 'section.')) {
            [, $section, $name] = array_pad(explode('.', $key, 3), 3, '');

            if ($name === 'enabled') {
                return ['name' => 'enabled', 'type' => 'toggle', 'default' => '1'];
            }

            $fields = config("site_settings.sections.{$section}.fields", []);
        } else {
            [$group, $name] = array_pad(explode('.', $key, 2), 2, '');
            $fields = config("site_settings.groups.{$group}.fields", []);
        }

        foreach ($fields as $field) {
            if ($field['name'] === $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Push the admin-edited values into Laravel's config so every existing `config('app.name')` /
     * `config('custom.*')` in views, mail and jobs follows the admin panel without any change.
     */
    /**
     * @param  bool  $draft  true = apply the unpublished draft (admin draft preview); false = the published site
     */
    public function applyToConfig(bool $draft = false): void
    {
        $has = fn (string $key) => $draft ? $this->has($key) : $this->hasPublished($key);
        $read = fn (string $key, ?string $default = null) => $draft ? $this->get($key, $default) : $this->published($key, $default);

        if ($has('general.app_name') && ($name = $read('general.app_name'))) {
            config(['app.name' => $name, 'mail.from.name' => $name]);
        }

        $map = [
            'general.owner_name' => 'owner_name',
            'general.owner_designation' => 'owner_designation',
            'general.owner_email' => 'owner_email',
            'general.owner_phone' => 'owner_phone',
            'contact.office_address' => 'office_address',
            'contact.office_phone' => 'office_phone',
            'contact.office_email' => 'office_email',
            'contact.whatsapp_number' => 'whatsapp_number',
            'contact.facebook_page_link' => 'facebook_page_link',
            'contact.facebook_page_name' => 'facebook_page_name',
        ];

        foreach ($map as $setting => $configKey) {
            if ($has($setting)) {
                config(["custom.{$configKey}" => (string) $read($setting, '')]);
            }
        }

        config([
            'custom.whatsapp_digits' => preg_replace('/\D+/', '', (string) config('custom.whatsapp_number')),
            'custom.office_phone_tel' => preg_replace('/[^\d+]/', '', (string) config('custom.office_phone')),
            'custom.owner_phone_tel' => preg_replace('/[^\d+]/', '', (string) config('custom.owner_phone')),
        ]);

        $this->applyMailConfig();
    }

    private function applyMailConfig(): void
    {
        $host = (string) $this->get('mail.host', '');

        if ($host === '') {
            return; // keep the server's .env mail configuration
        }

        $encryption = (string) $this->get('mail.encryption', 'tls');

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => (int) $this->get('mail.port', '587'),
            'mail.mailers.smtp.username' => (string) $this->get('mail.username', ''),
            'mail.mailers.smtp.password' => (string) $this->get('mail.password', ''),
            'mail.mailers.smtp.scheme' => $encryption === 'ssl' ? 'smtps' : 'smtp',
        ]);

        if ($from = $this->get('mail.from_address')) {
            config(['mail.from.address' => $from]);
        }

        if ($fromName = $this->get('mail.from_name')) {
            config(['mail.from.name' => $fromName]);
        }
    }
}
