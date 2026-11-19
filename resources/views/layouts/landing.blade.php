<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#072f31">

    @php
        $siteName = config('app.name');
        $pageTitle = $siteName . ' — Tours, Visa & Hotel Booking from Chattogram';
        $pageDesc = $siteName . ' plans tours, visa support, hotel and air ticket bookings across Bangladesh and the world — tailored itineraries, honest pricing and 24/7 support. Spreading smiles across the globe.';
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/banner.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://www.google.com">

    {{-- [data-reveal] is hidden only while JS runs. If the reveal code never starts
         (script blocked / failed to load), content is force-shown after 3s. --}}
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        (function (d) {
            d.classList.add('js');
            setTimeout(function () {
                if (!d.classList.contains('reveal-ready')) d.classList.add('reveal-failed');
            }, 3000);
        })(document.documentElement);
    </script>

    @vite([$css, 'resources/js/global.js'])

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'TravelAgency',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => asset('images/Logo.png'),
            'image' => asset('images/banner.jpg'),
            'description' => $pageDesc,
            'email' => config('custom.office_email'),
            'telephone' => config('custom.office_phone'),
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => config('custom.office_address'), 'addressCountry' => 'BD'],
            'sameAs' => [config('custom.facebook_page_link')],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
</head>

<body class="theme-{{ $theme }}">
<a class="skip-link" href="#main">Skip to content</a>

@include('partials.sections.nav')

<main id="main">
    @yield('hero')

    @include('partials.sections.about')
    @include('partials.sections.services')
    @include('partials.sections.journeys')
    @include('partials.sections.process')
    @include('partials.sections.trusted')
    @include('partials.sections.gallery')
    @include('partials.sections.reviews')
    @include('partials.sections.faq')
    @include('partials.sections.cta')
    @include('partials.sections.contact')
</main>

@include('partials.sections.footer')
@include('partials.sections.whatsapp')
</body>
</html>
