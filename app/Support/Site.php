<?php

namespace App\Support;

use App\Models\ContentItem;
use Illuminate\Support\Collection;

/**
 * Everything the public landing pages read: texts, switches, images and repeatable content.
 * Shared with every view as `$site` (see AppServiceProvider).
 *
 * The public site reads the last PUBLISHED snapshot (see Publisher). Before anything has been published, the
 * content the project shipped with (config/content_types.php) is shown, so a fresh checkout is never blank.
 */
class Site
{
    /** @var array<string, Collection> */
    private array $items = [];

    /** True while an admin previews their unpublished draft (see DraftPreview middleware). */
    private bool $draft = false;

    public function __construct(private readonly Settings $settings, private readonly Installer $installer) {}

    /** Drop the per-request item cache (called whenever content is saved, deleted or published). */
    public function forget(): void
    {
        $this->items = [];
    }

    /** Switch between the published website (default) and the admin's unpublished draft. */
    public function useDraft(bool $on): void
    {
        $this->draft = $on;
        $this->items = [];
    }

    public function isDraft(): bool
    {
        return $this->draft;
    }

    private function value(string $key, ?string $fallback = null): ?string
    {
        return $this->draft ? $this->settings->get($key, $fallback) : $this->settings->published($key, $fallback);
    }

    public function text(string $key, ?string $fallback = null): string
    {
        return $this->placeholders((string) $this->value($key, $fallback));
    }

    /** A page-section text, e.g. section('services', 'title'). */
    public function section(string $section, string $name, ?string $fallback = null): string
    {
        return $this->text("section.{$section}.{$name}", $fallback);
    }

    public function enabled(string $section): bool
    {
        $key = "section.{$section}.enabled";

        return $this->draft ? $this->settings->bool($key, true) : $this->settings->publishedBool($key, true);
    }

    public function theme(): string
    {
        return (string) $this->value('appearance.home_theme', 'chooser');
    }

    /** URL of a configured image (logo, favicon, banner, owner_avatar). */
    public function image(string $name): string
    {
        return (string) $this->url($this->value("branding.{$name}"));
    }

    public function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return preg_match('~^https?://~i', $path) ? $path : asset($path);
    }

    /** @return list<string> one entry per non-empty line */
    public function lines(string $key, array $fallback = []): array
    {
        $value = $this->value($key);

        if ($value === null || trim($value) === '') {
            return $fallback;
        }

        return array_values(array_filter(array_map(fn ($line) => $this->placeholders(trim($line)), preg_split('/\R/', $value) ?: []), fn ($line) => $line !== ''));
    }

    /** @return list<array{0: string, 1: string}> lines written as "A | B" */
    public function pairs(string $key, array $fallback = []): array
    {
        $lines = $this->lines($key);

        if ($lines === []) {
            return $fallback;
        }

        return array_map(function (string $line) {
            $parts = array_map('trim', explode('|', $line, 2));

            return [$parts[0], $parts[1] ?? ''];
        }, $lines);
    }

    /**
     * Active items of a content type, in display order, as plain arrays:
     * title + every data field, plus image_url / full_url / <field>_list where relevant.
     */
    public function items(string $type): Collection
    {
        return $this->items[$type] ??= $this->loadItems($type);
    }

    private function loadItems(string $type): Collection
    {
        $fields = collect(config("content_types.{$type}.fields", []));

        $published = $this->settings->snapshot();

        if ($this->draft) {
            $rows = ContentItem::ofType($type)->where('is_active', true)->ordered()->get()
                ->map(fn (ContentItem $item) => ['id' => $item->id, 'title' => $item->title] + ($item->data ?? []));
        } elseif ($published !== null) {
            $rows = collect($published['items'][$type] ?? [])->map(fn (array $row) => ['id' => $row['id'], 'title' => $row['title']] + ($row['data'] ?? []));
        } else {
            // Nothing published yet: the standard landing page that ships with the project.

            $rows = collect(ContentItem::defaultsFor($type))->map(fn (array $row, int $i) => ['id' => $i + 1, 'title' => $row[0]] + $row[1]);
        }

        return $rows->map(function (array $row) use ($fields, $type) {
            foreach ($fields as $field) {
                $name = $field['name'];

                if ($field['type'] === 'image') {
                    $row['image_url'] = $this->url($row[$name] ?? null);
                    $row['full_url'] = $type === 'gallery' ? $this->fullSize($row[$name] ?? null) : $row['image_url'];
                } elseif ($field['type'] === 'lines') {
                    $row["{$name}_list"] = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) ($row[$name] ?? '')) ?: [])));
                }
            }

            return $row;
        })->values();
    }

    /** Shipped gallery photos have a -1400 variant for the full-screen viewer; uploads do not. */
    private function fullSize(?string $path): ?string
    {
        if ($path && str_contains($path, '-800.') && is_file(public_path($full = str_replace('-800.', '-1400.', $path)))) {
            return $this->url($full);
        }

        return $this->url($path);
    }

    private function placeholders(string $text): string
    {
        return str_replace('{app_name}', (string) config('app.name'), $text);
    }
}
