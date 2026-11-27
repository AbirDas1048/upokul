<?php

/*
|--------------------------------------------------------------------------
| The product (not the customer's website)
|--------------------------------------------------------------------------
| This is the brand of the software itself. It appears on the first-time installation page and as a
| small "Powered by" credit in the admin panel. Each installation's OWN brand (site name, logo, ...) is
| chosen in the installer and edited under Site settings — it never replaces this.
|
| Reselling it under another name? Change PRODUCT_NAME (and the logo file) — nothing else.
*/

return [
    'name' => env('PRODUCT_NAME', 'Wayfolio'),
    'tagline' => env('PRODUCT_TAGLINE', 'Launch, brand and run your travel business website.'),
    'logo' => 'images/product/wayfolio-logo.svg',
    'favicon' => 'images/product/wayfolio-logo.svg',
];
