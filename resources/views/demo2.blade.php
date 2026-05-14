<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Upokul Travels & Holidays offers tour planning, visa support, hotel booking, and memorable travel experiences in Bangladesh and beyond.">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('home/images/favicon.ico') }}">

    <!-- Preconnect to speed up external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Preload hero image so it shows instantly -->
    <link rel="preload" as="image" href="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=1200&q=70&auto=format">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Poppins:wght@300;400;500;600&display=swap&font-display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ asset('home/css/demo2_base.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/nav.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/section.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/service.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/counter.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/gallery.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/review.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/contact.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/swal.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/whatsapp.css') }}">
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('home/js/common-interactions.js') }}"></script>
</body>
</html>
