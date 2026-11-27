<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#072f31">

    @php
        $siteName = config('app.name');
        $pageTitle = $site->text('seo.title') ?: $siteName . ' — Tours, Visa & Hotel Booking';
        $pageDesc = $site->text('seo.description') ?: $siteName . ' plans tours, visa support, hotel and air ticket bookings across Bangladesh and the world — tailored itineraries, honest pricing and 24/7 support. Spreading smiles across the globe.';
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ $site->image('favicon') }}">

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $site->image('banner') }}">
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
            '@@context' => 'https://schema.org',
            '@type' => $site->text('seo.business_type', 'TravelAgency'),
            'name' => $siteName,
            'url' => url('/'),
            'logo' => $site->image('logo'),
            'image' => $site->image('banner'),
            'description' => $pageDesc,
            'email' => config('custom.office_email'),
            'telephone' => config('custom.office_phone'),
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => config('custom.office_address')],
            'sameAs' => [config('custom.facebook_page_link')],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
</head>

<body class="theme-{{ $theme }}">
@if ($site->isDraft())
    <div style="position:fixed;z-index:99999;left:50%;bottom:14px;transform:translateX(-50%);background:#c9a24e;color:#231a05;font:600 13px/1 system-ui,sans-serif;padding:9px 16px;border-radius:99px;box-shadow:0 8px 24px rgba(0,0,0,.35);pointer-events:none">Draft preview — not published yet</div>
@endif
<a class="skip-link" href="#main">Skip to content</a>

@include('partials.install-welcome')

@include('partials.sections.nav')

<main id="main">
    @yield('hero')

    @if ($site->enabled('about')) @include('partials.sections.about') @endif
    @if ($site->enabled('services')) @include('partials.sections.services') @endif
    @if ($site->enabled('journeys')) @include('partials.sections.journeys') @endif
    @if ($site->enabled('process')) @include('partials.sections.process') @endif
    @if ($site->enabled('trusted')) @include('partials.sections.trusted') @endif
    @if ($site->enabled('gallery')) @include('partials.sections.gallery') @endif
    @if ($site->enabled('reviews')) @include('partials.sections.reviews') @endif
    @if ($site->enabled('faq')) @include('partials.sections.faq') @endif
    @if ($site->enabled('cta')) @include('partials.sections.cta') @endif
    @if ($site->enabled('contact')) @include('partials.sections.contact') @endif
</main>

@include('partials.sections.footer')
@include('partials.sections.whatsapp')
</body>
</html>
