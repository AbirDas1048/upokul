<?php

namespace App\Support;

use App\Models\ContentItem;
use App\Models\Setting;

/**
 * Draft → published. Everything an admin edits (texts, switches, images, content items, contact and owner
 * details, the chosen design) is a DRAFT. The public website only ever reads the last PUBLISHED snapshot,
 * which is replaced as a whole when an admin confirms "Publish". Until the first publish (a fresh checkout)
 * the website shows the standard landing page that ships with the project, so it is never blank.
 *
 * Not part of the snapshot: mail settings (operational, they apply immediately) and the install marker.
 */
class Publisher
{
    public const KEY = 'published.snapshot';

    public function __construct(
        private readonly Settings $settings,
        private readonly Site $site,
        private readonly Installer $installer,
    ) {}

    /** @return array{settings: array<string, string|null>, items: array<string, list<array>>} the current draft */
    public function draft(): array
    {
        $settings = Setting::query()->orderBy('key')->pluck('value', 'key')
            ->reject(fn ($value, string $key) => str_starts_with($key, 'published.') || str_starts_with($key, 'install.') || str_starts_with($key, 'mail.') || $key === 'installed_at')
            ->all();

        $items = [];

        foreach (array_keys(config('content_types')) as $type) {
            $items[$type] = ContentItem::ofType($type)->where('is_active', true)->ordered()->get()
                ->map(fn (ContentItem $item) => ['id' => $item->id, 'title' => $item->title, 'data' => $item->data ?? []])
                ->all();
        }

        return ['settings' => $settings, 'items' => $items];
    }

    public function publish(): void
    {
        $this->settings->set([
            self::KEY => json_encode($this->draft(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'published.at' => now()->toIso8601String(),
        ]);

        $this->site->forget();
    }

    /** Whether the draft differs from what visitors currently see. */
    public function isPending(): bool
    {
        $published = $this->settings->snapshot();

        if ($published === null) {
            return $this->installer->hasRun(); // installed but never published (should not normally happen)
        }

        return json_decode(json_encode($this->draft()), true) !== $published;
    }

    public function publishedAt(): ?string
    {
        return $this->settings->get('published.at');
    }
}
