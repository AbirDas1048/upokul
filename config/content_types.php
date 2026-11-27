<?php

/*
|--------------------------------------------------------------------------
| Repeatable website content (managed under "Website content" in the admin panel)
|--------------------------------------------------------------------------
| Each type is stored in the `content_items` table: `title` plus the other fields in a JSON `data`
| column. The key doubles as the permission module (config/permissions.php) and the admin URL
| (/admin/content/{type}). `defaults` is what a fresh install is seeded with — it is the content the
| website originally shipped with — and what the public site shows until the installer has run.
|
| Image fields hold a path relative to /public (uploads go to public/uploads).
*/

$dest = fn (string $slug) => "images/destinations/{$slug}-800.jpg";

$icon = ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'rules' => ['required', 'string', 'max:60', 'regex:/^fa-[a-z0-9-]+$/'], 'help' => 'A Font Awesome (free, solid) icon name such as fa-plane-departure. Browse them at fontawesome.com/icons.'];
$subject = ['name' => 'subject', 'label' => 'Enquiry subject', 'type' => 'text', 'rules' => 'nullable|string|max:100', 'help' => 'Pre-fills the "Interested in" box when a visitor clicks the enquiry link.'];

return [
    'services' => [
        'label' => 'Services', 'singular' => 'Service', 'icon' => 'fa-concierge-bell', 'section' => 'services',
        'title_label' => 'Service name', 'summary' => 'text', 'icon_field' => 'icon',
        'fields' => [
            $icon,
            ['name' => 'text', 'label' => 'Description', 'type' => 'textarea', 'rules' => 'required|string|max:300'],
            $subject,
        ],
        'defaults' => [
            ['Tour Planning', ['icon' => 'fa-route', 'text' => 'Custom itineraries built around your pace, budget and interests — domestic escapes to multi-country trips.', 'subject' => 'Tour Planning']],
            ['Hotel Booking', ['icon' => 'fa-hotel', 'text' => 'Hand-picked stays at fair rates, chosen for comfort, safety and location.', 'subject' => 'Hotel Booking']],
            ['Air Ticket Booking', ['icon' => 'fa-plane-departure', 'text' => 'Flexible, competitively priced flights with trusted airlines worldwide.', 'subject' => 'Air Ticket Booking']],
            ['Visa Processing', ['icon' => 'fa-passport', 'text' => 'Clear guidance on documents and timelines, with expert help at every step.', 'subject' => 'Visa Processing']],
            ['Transport & Transfers', ['icon' => 'fa-van-shuttle', 'text' => 'Safe, comfortable airport pick-ups, city transfers and tour transport.', 'subject' => 'Transport & Transfers']],
            ['Travel Insurance', ['icon' => 'fa-shield-heart', 'text' => 'Dependable cover so you can travel with real peace of mind.', 'subject' => 'Travel Insurance']],
        ],
    ],

    'journeys' => [
        'label' => 'Journeys', 'singular' => 'Journey', 'icon' => 'fa-route', 'section' => 'journeys',
        'title_label' => 'Journey title', 'summary' => 'subtitle', 'image_field' => 'image',
        'fields' => [
            ['name' => 'tag', 'label' => 'Tag', 'type' => 'text', 'rules' => 'required|string|max:40', 'col' => 6, 'help' => 'e.g. Domestic, International, Couples.'],
            $subject + ['col' => 6],
            ['name' => 'subtitle', 'label' => 'Short line', 'type' => 'text', 'rules' => 'required|string|max:120'],
            ['name' => 'points', 'label' => 'Bullet points', 'type' => 'lines', 'rules' => 'required|string|max:600', 'help' => 'One point per line (3 works best).'],
            ['name' => 'image', 'label' => 'Photo', 'type' => 'image', 'rules' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096', 'help' => 'Portrait photo, around 800 × 1000 px.'],
        ],
        'defaults' => [
            ['Escapes in Bangladesh', ['tag' => 'Domestic', 'subtitle' => 'Beach, hills & heritage getaways', 'points' => "Coastal & hill-country escapes\nComfortable transport & stays\nWeekend to week-long plans", 'image' => $dest('sunset-shores'), 'subject' => 'Domestic tour']],
            ['World Tours', ['tag' => 'International', 'subtitle' => 'Handled start to finish', 'points' => "Visa & documentation guidance\nFlights, hotels & transfers\nLocal-expert recommendations", 'image' => $dest('temples-of-bali'), 'subject' => 'International tour']],
            ['Honeymoon Retreats', ['tag' => 'Couples', 'subtitle' => 'Romantic, private, effortless', 'points' => "Sunset-view stays\nPrivate transfers & experiences\nSurprise-ready planning", 'image' => $dest('bali-sunsets'), 'subject' => 'Honeymoon package']],
            ['Family & Group Holidays', ['tag' => 'Groups', 'subtitle' => 'Fun for every age', 'points' => "Flexible, kid-friendly pacing\nGroup-friendly rooms & transport\nOne point of contact for all", 'image' => $dest('golden-coastlines'), 'subject' => 'Family / group holiday']],
        ],
    ],

    'process_steps' => [
        'label' => 'How it works', 'singular' => 'Step', 'icon' => 'fa-list-ol', 'section' => 'process',
        'title_label' => 'Step title', 'summary' => 'text', 'icon_field' => 'icon',
        'fields' => [
            $icon,
            ['name' => 'text', 'label' => 'Description', 'type' => 'textarea', 'rules' => 'required|string|max:300'],
        ],
        'defaults' => [
            ['Tell Us Your Plan', ['icon' => 'fa-comments', 'text' => 'Share where, when and how you like to travel — a quick call or message is all it takes.']],
            ['Get a Tailored Itinerary', ['icon' => 'fa-map-location-dot', 'text' => 'We design options with clear costs, so you can compare and choose with confidence.']],
            ['We Handle the Details', ['icon' => 'fa-passport', 'text' => 'Visa guidance, flights, hotels and transfers — booked and confirmed for you.']],
            ['Travel With Support', ['icon' => 'fa-suitcase-rolling', 'text' => 'Go and enjoy. Our team stays reachable around the clock if anything comes up.']],
        ],
    ],

    'stats' => [
        'label' => 'Trust numbers', 'singular' => 'Number', 'icon' => 'fa-chart-simple', 'section' => 'trusted',
        'title_label' => 'Label', 'summary' => 'count', 'icon_field' => 'icon',
        'fields' => [
            $icon,
            ['name' => 'count', 'label' => 'Number', 'type' => 'number', 'rules' => 'required|integer|min:0|max:99999999', 'col' => 6],
            ['name' => 'suffix', 'label' => 'Suffix', 'type' => 'text', 'rules' => 'nullable|string|max:5', 'col' => 6, 'help' => 'e.g. + or %'],
        ],
        'defaults' => [
            ['Satisfied Clients', ['icon' => 'fa-face-smile-beam', 'count' => 100, 'suffix' => '+']],
            ['Destinations Covered', ['icon' => 'fa-earth-asia', 'count' => 30, 'suffix' => '+']],
            ['Hotel Partnerships', ['icon' => 'fa-hotel', 'count' => 800, 'suffix' => '+']],
            ['Trusted Agents & Media', ['icon' => 'fa-handshake', 'count' => 500, 'suffix' => '+']],
        ],
    ],

    'gallery' => [
        'label' => 'Gallery', 'singular' => 'Photo', 'icon' => 'fa-images', 'section' => 'gallery',
        'title_label' => 'Photo title', 'summary' => 'category', 'image_field' => 'image',
        'fields' => [
            ['name' => 'category', 'label' => 'Category / caption', 'type' => 'text', 'rules' => 'required|string|max:60', 'help' => 'e.g. Beach Holidays.'],
            ['name' => 'alt', 'label' => 'Alt text (for screen readers)', 'type' => 'text', 'rules' => 'required|string|max:160'],
            ['name' => 'image', 'label' => 'Photo', 'type' => 'image', 'rules' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:6144', 'help' => 'Landscape photo, at least 1400 px wide.'],
        ],
        // Built from config/destinations.php (the photos that ship with the site).
        'defaults' => 'destinations',
    ],

    'reviews' => [
        'label' => 'Reviews', 'singular' => 'Review', 'icon' => 'fa-star', 'section' => 'reviews',
        'title_label' => 'Reviewer name', 'summary' => 'quote',
        'fields' => [
            ['name' => 'source', 'label' => 'Where was it posted?', 'type' => 'select', 'rules' => 'required|in:google,facebook', 'options' => ['google' => 'Google', 'facebook' => 'Facebook']],
            ['name' => 'quote', 'label' => 'Review text', 'type' => 'textarea', 'rules' => 'required|string|max:500'],
        ],
        'defaults' => [
            ['Rahim Ahmed', ['source' => 'google', 'quote' => 'Amazing service! Visa processing and hotel booking were handled so smoothly. Highly recommended to everyone.']],
            ['Nusrat Jahan', ['source' => 'google', 'quote' => 'Very professional team. Our family tour was perfectly planned and completely stress-free. Will book again!']],
            ['Tanvir Hasan', ['source' => 'google', 'quote' => 'Best travel agency in Bangladesh. Quick support, transparent pricing, and they genuinely care about travelers.']],
            ['Farida Begum', ['source' => 'facebook', 'quote' => 'They handled everything from flights to hotels perfectly. The whole trip was magical — absolutely loved it!']],
            ['Karim & Sadia', ['source' => 'facebook', 'quote' => 'Exceptional experience. The team was attentive and made our honeymoon absolutely unforgettable.']],
        ],
    ],

    'faqs' => [
        'label' => 'FAQs', 'singular' => 'FAQ', 'icon' => 'fa-circle-question', 'section' => 'faq',
        'title_label' => 'Question', 'summary' => 'answer',
        'fields' => [
            ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea', 'rows' => 5, 'rules' => 'required|string|max:1000'],
        ],
        'defaults' => [
            ['How early should I plan my trip?', ['answer' => 'The earlier the better — it widens your choice of flights, hotels and dates, especially in peak seasons and for visa-dependent destinations. That said, talk to us whenever you are ready and we will tell you honestly what is possible.']],
            ['Can you help with visa processing?', ['answer' => 'Yes. We guide you on the documents required, help you prepare your application and keep you updated on progress. Requirements and timelines vary by country, so we confirm them with you at the start.']],
            ['Can I customize a package?', ['answer' => 'Absolutely. Every trip is built around you — destinations, hotel style, pace, activities and budget. Tell us what matters and we will shape an itinerary to match.']],
            ['Do you arrange domestic as well as international trips?', ['answer' => 'Yes. We plan escapes within Bangladesh as well as international holidays, including honeymoon, family and group travel.']],
            ['What if I need help while I am travelling?', ['answer' => 'Our team is reachable around the clock. Message us on WhatsApp or call the office and a real person will help you sort it out.']],
            ['How do I get a quote?', ['answer' => 'Send us a message using the form below or on WhatsApp with your destination, rough dates and number of travelers. We will come back with clear options and costs.']],
        ],
    ],

    'about_points' => [
        'label' => 'About highlights', 'singular' => 'Highlight', 'icon' => 'fa-circle-info', 'section' => 'about',
        'title_label' => 'Heading', 'summary' => 'text', 'icon_field' => 'icon',
        'fields' => [
            $icon,
            ['name' => 'text', 'label' => 'Short text', 'type' => 'text', 'rules' => 'required|string|max:120'],
        ],
        'defaults' => [
            ['Experienced experts', ['icon' => 'fa-user-shield', 'text' => 'Trusted advisors who know every route.']],
            ['Tailor-made trips', ['icon' => 'fa-wand-magic-sparkles', 'text' => 'Itineraries shaped around you.']],
            ['Transparent pricing', ['icon' => 'fa-tags', 'text' => 'Clear quotes with no surprises.']],
            ['24/7 support', ['icon' => 'fa-headset', 'text' => 'Real people, before and during travel.']],
        ],
    ],
];
