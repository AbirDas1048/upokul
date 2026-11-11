<?php

return [
    'owner_name' => env('OWNER_NAME', 'Abir Das'),
    'owner_designation' => env('OWNER_DESIGNATION', 'Owner'),
    'owner_email' => env('OWNER_EMAIL', 'abirdas....@email.com'),
    'owner_phone' => env('OWNER_PHONE', '018xx-xxxxxx'),
    'office_address' => env('OFFICE_ADDRESS', 'Chattogram'),
    'office_phone' => env('OFFICE_PHONE', '013-xxxxxx'),
    'office_email' => env('OFFICE_EMAIL', 'office@email.com'),
    'facebook_page_link' => env('FACEBOOK_PAGE_LINK', 'https://www.facebook.com'),
    'facebook_page_name' => env('FACEBOOK_PAGE_NAME', 'Facebook'),
    'whatsapp_number' => env('WHATSAPP_NUMBER', '018xx-xxxxxx'),

    // Digits-only / dial-safe variants for wa.me and tel: links
    // (wa.me rejects "+", spaces and dashes).
    'whatsapp_digits' => preg_replace('/\D+/', '', (string) env('WHATSAPP_NUMBER', '')),
    'office_phone_tel' => preg_replace('/[^\d+]/', '', (string) env('OFFICE_PHONE', '')),
    'owner_phone_tel' => preg_replace('/[^\d+]/', '', (string) env('OWNER_PHONE', '')),

    // Contact-form abuse protection (see App\Services\ContactSpamGuard + the "contact" rate limiter).
    'contact' => [
        // Per visitor IP.
        'per_minute' => (int) env('CONTACT_LIMIT_PER_MINUTE', 3),
        'per_hour' => (int) env('CONTACT_LIMIT_PER_HOUR', 10),
        // Per e-mail address: stops the "thank you" mail being used to mail-bomb someone else.
        'per_email_hour' => (int) env('CONTACT_LIMIT_PER_EMAIL_HOUR', 3),
        // Whole site, all visitors: caps a distributed flood.
        'global_per_hour' => (int) env('CONTACT_LIMIT_GLOBAL_PER_HOUR', 200),
        // A human cannot fill 5 fields faster than this; bots usually do.
        'min_seconds' => (int) env('CONTACT_MIN_FILL_SECONDS', 3),
        // The signed form token expires after this long (page must be reloaded).
        'token_max_hours' => (int) env('CONTACT_TOKEN_MAX_HOURS', 6),
        // The same message from the same address inside this window is treated as a double-submit.
        'duplicate_minutes' => (int) env('CONTACT_DUPLICATE_MINUTES', 10),
        // Spam is mostly links; plain enquiries rarely need more than one.
        'max_links' => (int) env('CONTACT_MAX_LINKS', 1),
    ],
];
