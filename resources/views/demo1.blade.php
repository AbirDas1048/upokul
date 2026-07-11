<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    @vite([
        'resources/css/demo1.css',
        'resources/js/global.js'
    ])
</head>

<body>

<!-- Navbar -->
@include('partials.sections.nav')

<!-- Hero Section -->
<section class="hero-section">
    <video class="hero-video" autoplay muted loop playsinline tabindex="-1" preload="metadata" poster="{{ asset('images/banner.jpg') }}">
        <source src="{{ asset('videos/bg.mp4') }}" type="video/mp4">
    </video>

    <div class="hero-overlay"></div>

    <div class="container">
        <div class="row">
            <div class="col-lg-6 hero-content">
                <h1>
                    Discover Beautiful Places <br>
                    <span>With {{ config('app.name') }}</span>
                </h1>

                <p>
                    We create unforgettable travel experiences with comfort,
                    safety and trusted service across Bangladesh & beyond.
                </p>

                <div class="hero-buttons">
                    <a href="#services" class="btn premium-btn premium-btn-pill premium-btn-glossy-primary">
                        <i class="fas fa-route"></i> Our Services
                    </a>
                    <a href="#gallery" class="btn premium-btn premium-btn-pill premium-btn-glossy-secondary">
                        <i class="fa-solid fa-magnifying-glass-location"></i> Explore Places
                    </a>
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
