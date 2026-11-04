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
];
