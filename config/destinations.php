<?php

/*
|--------------------------------------------------------------------------
| Destination photos
|--------------------------------------------------------------------------
| Single source of truth for the gallery and journey cards.
| Files live in public/images/destinations/{slug}-800.jpg (cards/thumbnails)
| and {slug}-1400.jpg (full-screen viewer). To replace a photo, overwrite the
| two files with the same name; to add one, add a row here plus the two files.
*/

return [
    'path' => 'images/destinations',

    'photos' => [
        'sunset-shores' => ['title' => 'Sunset Shores', 'category' => 'Beach Holidays', 'alt' => 'Sunset over a calm tropical beach'],
        'golden-coastlines' => ['title' => 'Golden Coastlines', 'category' => 'Beach Holidays', 'alt' => 'Sandy cove between sandstone cliffs'],
        'alpine-lakes' => ['title' => 'Alpine Lakes', 'category' => 'Mountain Escapes', 'alt' => 'Rowing boat on a turquoise mountain lake'],
        'lakeside-calm' => ['title' => 'Lakeside Calm', 'category' => 'Mountain Escapes', 'alt' => 'View from a wooden boat across a mountain lake'],
        'bali-sunsets' => ['title' => 'Bali Sunsets', 'category' => 'Indonesia', 'alt' => 'Temple on a sea rock at sunset in Bali'],
        'temples-of-bali' => ['title' => 'Temples of Bali', 'category' => 'Indonesia', 'alt' => 'Multi-tiered temple beside a lake in Bali'],
    ],
];
