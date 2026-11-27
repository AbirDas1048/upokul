<?php

/*
|--------------------------------------------------------------------------
| Admin-editable settings schema
|--------------------------------------------------------------------------
| Drives the Settings, Appearance and Page-sections screens: each field becomes a form control,
| is validated with `rules` and stored in the `settings` table under "<group>.<name>"
| (sections: "section.<id>.<name>"). A field with no stored value falls back to `default`.
|
| Field types: text, textarea, email, url, number, select, toggle, image, password, lines.
| Text may contain {app_name}, replaced with the site name when rendered.
| `allow_blank` => true: an empty value is kept as empty (e.g. "no map") instead of falling back to the default.
*/

$heading = fn (string $eyebrow, string $title, string $highlight, string $subtitle, string $subLabel = 'Subtitle') => [
    ['name' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'rules' => 'nullable|string|max:60', 'col' => 4, 'default' => $eyebrow],
    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'nullable|string|max:120', 'col' => 4, 'default' => $title],
    ['name' => 'highlight', 'label' => 'Highlighted part', 'type' => 'text', 'rules' => 'nullable|string|max:120', 'col' => 4, 'default' => $highlight],
    ['name' => 'subtitle', 'label' => $subLabel, 'type' => 'textarea', 'rules' => 'nullable|string|max:300', 'default' => $subtitle],
];

return [
    // Setting keys whose value is stored encrypted.
    'encrypted' => ['mail.password'],

    'groups' => [
        'general' => [
            'label' => 'General', 'icon' => 'fa-house', 'permission' => 'settings',
            'description' => 'Your project name and the owner details shown across the website.',
            'fields' => [
                ['name' => 'app_name', 'label' => 'Project / site name', 'type' => 'text', 'rules' => 'required|string|max:100', 'help' => 'Also written to APP_NAME in .env.', 'default' => env('APP_NAME', 'My Travel Business')],
                ['name' => 'tagline', 'allow_blank' => true, 'label' => 'Tagline', 'type' => 'text', 'rules' => 'nullable|string|max:150', 'default' => 'Spreading smiles across the globe'],
                ['name' => 'owner_name', 'label' => 'Owner name', 'type' => 'text', 'rules' => 'required|string|max:100', 'col' => 6, 'default' => ''],
                ['name' => 'owner_designation', 'label' => 'Owner designation', 'type' => 'text', 'rules' => 'nullable|string|max:60', 'col' => 6, 'default' => 'Owner'],
                ['name' => 'owner_email', 'label' => 'Owner email', 'type' => 'email', 'rules' => 'required|email|max:150', 'col' => 6, 'default' => ''],
                ['name' => 'owner_phone', 'label' => 'Owner phone', 'type' => 'text', 'rules' => 'nullable|string|max:30', 'col' => 6, 'default' => ''],
            ],
        ],
        'contact' => [
            'label' => 'Contact & social', 'icon' => 'fa-address-book', 'permission' => 'settings',
            'description' => 'Office details, WhatsApp, Facebook and review links shown on the website.',
            'fields' => [
                ['name' => 'office_address', 'label' => 'Office address', 'type' => 'textarea', 'rules' => 'nullable|string|max:300', 'default' => ''],
                ['name' => 'office_phone', 'label' => 'Office phone', 'type' => 'text', 'rules' => 'nullable|string|max:30', 'col' => 6, 'default' => ''],
                ['name' => 'office_email', 'label' => 'Office email', 'type' => 'email', 'rules' => 'required|email|max:150', 'col' => 6, 'help' => 'Contact-form messages are mailed here.', 'default' => ''],
                ['name' => 'whatsapp_number', 'label' => 'WhatsApp number', 'type' => 'text', 'rules' => 'nullable|string|max:30', 'col' => 6, 'default' => ''],
                ['name' => 'facebook_page_name', 'label' => 'Facebook page name', 'type' => 'text', 'rules' => 'nullable|string|max:100', 'col' => 6, 'default' => 'Facebook'],
                ['name' => 'facebook_page_link', 'label' => 'Facebook page URL', 'type' => 'url', 'rules' => 'nullable|url|max:300', 'default' => 'https://www.facebook.com'],
                ['name' => 'google_review_url', 'allow_blank' => true, 'label' => 'Google review URL', 'type' => 'url', 'rules' => 'nullable|url|max:500', 'col' => 6, 'default' => ''],
                ['name' => 'facebook_review_url', 'allow_blank' => true, 'label' => 'Facebook review URL', 'type' => 'url', 'rules' => 'nullable|url|max:500', 'col' => 6, 'default' => ''],
                ['name' => 'map_embed_url', 'allow_blank' => true, 'label' => 'Google Maps embed URL', 'type' => 'url', 'rules' => ['nullable', 'url', 'max:1500', 'starts_with:https://www.google.com/maps/embed'], 'help' => 'Google Maps → Share → Embed a map → copy the src="..." address. Leave empty to hide the map.', 'default' => ''],
            ],
        ],
        'branding' => [
            'label' => 'Logo & images', 'icon' => 'fa-image', 'permission' => 'settings',
            'description' => 'Upload your own logo, favicon, hero banner and owner photo. Leave a field alone to keep the current image.',
            'fields' => [
                ['name' => 'logo', 'label' => 'Logo', 'type' => 'image', 'rules' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048', 'col' => 6, 'default' => 'images/default-logo.svg', 'help' => 'Square PNG, at least 420 × 420 px.'],
                ['name' => 'favicon', 'label' => 'Favicon', 'type' => 'image', 'rules' => 'nullable|mimes:ico,png|max:512', 'col' => 6, 'default' => 'images/default-logo.svg', 'help' => '.ico or .png, 64 × 64 px.'],
                ['name' => 'banner', 'label' => 'Hero banner / social image', 'type' => 'image', 'rules' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096', 'col' => 6, 'default' => 'images/banner.jpg', 'help' => 'Wide photo, at least 1600 px.'],
                ['name' => 'owner_avatar', 'label' => 'Owner photo', 'type' => 'image', 'rules' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048', 'col' => 6, 'default' => 'images/avatar.png', 'help' => 'Square photo.'],
            ],
        ],
        'seo' => [
            'label' => 'SEO', 'icon' => 'fa-magnifying-glass-chart', 'permission' => 'settings',
            'description' => 'What search engines and social networks show for your website.',
            'fields' => [
                ['name' => 'title', 'label' => 'Page title', 'type' => 'text', 'rules' => 'nullable|string|max:70', 'help' => 'Blank = "<site name> — Tours, Visa & Hotel Booking".', 'default' => ''],
                ['name' => 'description', 'label' => 'Meta description', 'type' => 'textarea', 'rules' => 'nullable|string|max:300', 'help' => 'Around 150 characters. Blank = a sensible default.', 'default' => ''],
                ['name' => 'business_type', 'label' => 'Schema.org business type', 'type' => 'text', 'rules' => 'nullable|string|max:50', 'help' => 'e.g. TravelAgency, LocalBusiness.', 'default' => 'TravelAgency'],
            ],
        ],
        'mail' => [
            'label' => 'Mail (SMTP)', 'icon' => 'fa-paper-plane', 'permission' => 'mail',
            'description' => 'Outgoing mail for contact-form messages and thank-you replies. Leave the host empty to keep the server defaults from .env.',
            'fields' => [
                ['name' => 'host', 'label' => 'SMTP host', 'type' => 'text', 'rules' => 'nullable|string|max:150', 'col' => 8, 'default' => ''],
                ['name' => 'port', 'label' => 'Port', 'type' => 'number', 'rules' => 'nullable|integer|between:1,65535', 'col' => 4, 'default' => '587'],
                ['name' => 'username', 'label' => 'Username', 'type' => 'text', 'rules' => 'nullable|string|max:150', 'col' => 6, 'default' => ''],
                ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'rules' => 'nullable|string|max:200', 'col' => 6, 'help' => 'Stored encrypted. Leave blank to keep the current password.', 'default' => ''],
                ['name' => 'encryption', 'label' => 'Encryption', 'type' => 'select', 'rules' => 'nullable|in:tls,ssl,none', 'col' => 4, 'options' => ['tls' => 'TLS (STARTTLS, port 587)', 'ssl' => 'SSL (port 465)', 'none' => 'None'], 'default' => 'tls'],
                ['name' => 'from_address', 'label' => 'From address', 'type' => 'email', 'rules' => 'nullable|email|max:150', 'col' => 4, 'default' => ''],
                ['name' => 'from_name', 'label' => 'From name', 'type' => 'text', 'rules' => 'nullable|string|max:100', 'col' => 4, 'default' => ''],
            ],
        ],
        'appearance' => [
            'label' => 'Appearance', 'icon' => 'fa-palette', 'permission' => 'appearance',
            'description' => 'Choose which design visitors see on the home page.',
            'fields' => [
                ['name' => 'home_theme', 'label' => 'Home page design', 'type' => 'select', 'rules' => 'required|in:chooser,aurora,noir,glass', 'default' => 'chooser', 'options' => [
                    'chooser' => 'Design chooser (shows all three demos)',
                    'aurora' => 'Aurora — bright & airy',
                    'noir' => 'Noir — dark & elegant',
                    'glass' => 'Glass — modern translucent',
                ]],
            ],
        ],
    ],

    /*
    | Page sections. Each one has a switch and its own texts; repeatable content (service cards,
    | FAQs...) is managed under "Website content" (config/content_types.php).
    | Hero fields left blank fall back to the chosen design's own wording.
    */
    'sections' => [
        'hero' => [
            'label' => 'Hero', 'icon' => 'fa-panorama', 'toggle' => false,
            'fields' => [
                ['name' => 'eyebrow', 'label' => 'Small line above the title', 'type' => 'text', 'rules' => 'nullable|string|max:120', 'help' => 'Blank = the design\'s default.'],
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'nullable|string|max:120', 'col' => 6, 'help' => 'Blank = "Discover Beautiful Places".'],
                ['name' => 'highlight', 'label' => 'Highlighted part of the title', 'type' => 'text', 'rules' => 'nullable|string|max:120', 'col' => 6, 'help' => 'Shown in the accent colour.'],
                ['name' => 'lead', 'label' => 'Intro paragraph', 'type' => 'textarea', 'rules' => 'nullable|string|max:400'],
                ['name' => 'primary_label', 'label' => 'Primary button text', 'type' => 'text', 'rules' => 'nullable|string|max:40', 'col' => 6],
                ['name' => 'secondary_label', 'label' => 'Secondary button text', 'type' => 'text', 'rules' => 'nullable|string|max:40', 'col' => 6],
                ['name' => 'chips', 'label' => 'Tick-list under the buttons (Aurora)', 'type' => 'lines', 'rules' => 'nullable|string|max:500', 'help' => 'One short item per line.', 'default' => "Visa support\nHand-picked hotels\n24/7 assistance"],
                ['name' => 'aside_title', 'label' => 'Side card title (Noir / Glass)', 'type' => 'text', 'rules' => 'nullable|string|max:80'],
                ['name' => 'aside_items', 'label' => 'Side card items (Noir / Glass)', 'type' => 'lines', 'rules' => 'nullable|string|max:900', 'help' => 'One per line as: Heading | Short text'],
                ['name' => 'strip', 'label' => 'Number strip (Noir)', 'type' => 'lines', 'rules' => 'nullable|string|max:400', 'help' => 'One per line as: 100+ | Satisfied Clients', 'default' => "100+ | Satisfied Clients\n30+ | Destinations\n800+ | Hotel Partners\n24/7 | Support"],
            ],
        ],
        'about' => [
            'label' => 'About', 'icon' => 'fa-circle-info', 'content' => 'about_points',
            'fields' => [
                ['name' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'rules' => 'nullable|string|max:60', 'col' => 4, 'default' => 'About Us'],
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'nullable|string|max:120', 'col' => 4, 'default' => 'Your Trusted Partner for'],
                ['name' => 'highlight', 'label' => 'Highlighted part', 'type' => 'text', 'rules' => 'nullable|string|max:120', 'col' => 4, 'default' => 'Unforgettable Journeys'],
                ['name' => 'body', 'label' => 'Description', 'type' => 'textarea', 'rows' => 5, 'rules' => 'nullable|string|max:1500', 'default' => '{app_name} is a travel house dedicated to meaningful trips — from sun-soaked coastlines and misty hill escapes to international holidays. We pair local know-how with careful planning so every detail, from visa to check-in, is handled and you simply enjoy the moment.'],
                ['name' => 'tag', 'label' => 'Badge on the logo card', 'type' => 'text', 'rules' => 'nullable|string|max:80', 'default' => 'Spreading smiles across the globe'],
            ],
        ],
        'services' => [
            'label' => 'Services', 'icon' => 'fa-concierge-bell', 'content' => 'services',
            'fields' => $heading('Our Services', 'Everything You Need for a', 'Perfect Journey', 'One team for every step — planning, booking, paperwork and on-trip support.'),
        ],
        'journeys' => [
            'label' => 'Journeys', 'icon' => 'fa-route', 'content' => 'journeys',
            'fields' => $heading('Signature Journeys', 'Find the Trip That', 'Feels Like You', 'Start from a style of travel — we\'ll shape the details, dates and budget together.'),
        ],
        'process' => [
            'label' => 'How it works', 'icon' => 'fa-list-ol', 'content' => 'process_steps',
            'fields' => $heading('How It Works', 'From First Hello to', 'Happy Return', 'A simple, transparent process — four steps between you and your next adventure.'),
        ],
        'trusted' => [
            'label' => 'Trust numbers', 'icon' => 'fa-chart-simple', 'content' => 'stats',
            'fields' => $heading('Why Travelers Choose Us', 'Trusted by Travelers', 'Everywhere', 'Numbers that reflect our experience, our partnerships and the trust placed in us.'),
        ],
        'gallery' => [
            'label' => 'Gallery', 'icon' => 'fa-images', 'content' => 'gallery',
            'fields' => $heading('Travel Moments', 'Explore Our', 'Destinations', 'A glimpse of the places our travelers fall in love with. Tap any photo to view it full screen.'),
        ],
        'reviews' => [
            'label' => 'Reviews', 'icon' => 'fa-star', 'content' => 'reviews',
            'fields' => $heading('Testimonials', 'What Our Travelers', 'Say', 'Kind words from our Google Business and Facebook pages.'),
        ],
        'faq' => [
            'label' => 'FAQ', 'icon' => 'fa-circle-question', 'content' => 'faqs',
            'fields' => $heading('FAQ', 'Questions,', 'Answered', 'Everything travelers usually ask before booking. Can\'t find yours? We\'re happy to help.', 'Intro'),
        ],
        'cta' => [
            'label' => 'Call-to-action band', 'icon' => 'fa-bullhorn',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'nullable|string|max:120', 'col' => 6, 'default' => 'Ready for your next'],
                ['name' => 'highlight', 'label' => 'Highlighted part', 'type' => 'text', 'rules' => 'nullable|string|max:120', 'col' => 6, 'default' => 'adventure?'],
                ['name' => 'subtitle', 'label' => 'Text', 'type' => 'textarea', 'rules' => 'nullable|string|max:300', 'default' => 'Tell us where you dream of going. We\'ll take care of the rest and keep the smiles coming.'],
            ],
        ],
        'contact' => [
            'label' => 'Contact form', 'icon' => 'fa-envelope',
            'fields' => $heading('Get In Touch', 'Let\'s Plan Your', 'Next Trip', 'Send us a few details and our team will get back to you with options and a clear quote.'),
        ],
        'footer' => [
            'label' => 'Footer', 'icon' => 'fa-shoe-prints', 'toggle' => false,
            'fields' => [
                ['name' => 'about', 'label' => 'Footer description', 'type' => 'textarea', 'rules' => 'nullable|string|max:400', 'default' => 'Tours, visas, hotels and flights — thoughtfully planned and personally supported. Spreading smiles across the globe.'],
            ],
        ],
    ],
];
