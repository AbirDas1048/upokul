<?php

namespace App\Models;

use App\Support\Site;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContentItem extends Model
{
    protected $fillable = ['type', 'title', 'data', 'sort_order', 'is_active'];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        $forget = fn () => app(Site::class)->forget();
        static::saved($forget);
        static::deleted($forget);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * The content the website ships with (config/content_types.php), as rows ready to insert.
     *
     * @return list<array{0: string, 1: array}> [title, data] pairs
     */
    public static function defaultsFor(string $type): array
    {
        $defaults = config("content_types.{$type}.defaults", []);

        if ($defaults === 'destinations') {
            $defaults = [];
            $path = config('destinations.path');

            foreach (config('destinations.photos') as $slug => $photo) {
                $defaults[] = [$photo['title'], ['category' => $photo['category'], 'alt' => $photo['alt'], 'image' => "{$path}/{$slug}-800.jpg"]];
            }
        }

        return $defaults;
    }

    /** Fill an empty type with its default content. Returns how many rows were created. */
    public static function seedDefaults(string $type): int
    {
        if (self::ofType($type)->exists()) {
            return 0;
        }

        foreach (self::defaultsFor($type) as $i => [$title, $data]) {
            self::create(['type' => $type, 'title' => $title, 'data' => $data, 'sort_order' => ($i + 1) * 10, 'is_active' => true]);
        }

        return count(self::defaultsFor($type));
    }
}
