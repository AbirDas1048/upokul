<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Upokul Travels & Holidays offers tour planning, visa support, hotel booking, and memorable travel experiences in Bangladesh and beyond.">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <!-- Preconnect to speed up external resources -->
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    @vite([
        'resources/css/demo2.css',
        'resources/js/global.js'
    ])
</head>
<body>

<!-- Navbar -->
@include('partials.sections.nav')

<!-- ░░░ HERO ░░░ -->
<section id="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-noise"></div>
    <div class="hero-grid"></div>
    <div class="hero-orb orb-a"></div>
    <div class="hero-orb orb-b"></div>

    <div class="hero-container container">
        <div class="hero-content">
            <div class="hero-eyebrow">Trusted Travel Experts — Bangladesh & Beyond</div>
            <h1>
                Discover Beautiful Places
                <em>With {{ config('app.name') }}</em>
            </h1>
            <p>We create unforgettable travel experiences with comfort, safety and trusted service. Your perfect journey starts here.</p>
            <div class="hero-btns">
                <a href="#services" class="btn btn-primary"><i class="fas fa-route"></i> Our Services</a>
                <a href="#gallery"  class="btn btn-outline"><i class="fas fa-magnifying-glass-location"></i> Explore Places</a>
            </div>
        </div>
    </div>

    <!-- Stats strip -->
    <div class="hero-stats">
        <div class="container">
            <div class="hero-stats-inner">
                <div class="hero-stat">
                    <div class="hero-stat-num">100+</div>
                    <div class="hero-stat-lbl">Satisfied Clients</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num">30+</div>
                    <div class="hero-stat-lbl">Destinations</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num">800+</div>
                    <div class="hero-stat-lbl">Hotel Partners</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num">4.9★</div>
                    <div class="hero-stat-lbl">Customer Rating</div>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- About Section -->
@include('partials.sections.about')

<!-- Services Section -->
@include('partials.sections.services')

<!-- Counter Section -->
@include('partials.sections.trusted')

<!-- Gallery Section -->
@include('partials.sections.gallery')

<!-- Review Section -->
@include('partials.sections.reviews')

<!-- Contact Section -->
@include('partials.sections.contact')

<!-- Footer Section -->
@include('partials.sections.footer')

<!-- Floating WhatsApp Button -->
@include('partials.sections.whatsapp')

</body>
</html>
