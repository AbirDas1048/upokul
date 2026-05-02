<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('home/images/favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Google Fonts -->
    {{--    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">--}}
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Swiper Library CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('new/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/nav.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/service.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/counter.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/gallery.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/review.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/contact.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/whatsapp.css') }}">
</head>

<body>

<!-- Navbar -->
{{--<nav id="mainNav" class="navbar navbar-expand-lg fixed-top navbar-dark">--}}
{{--    <div class="container">--}}
{{--        <a class="navbar-brand fw-bold" href="#hero">--}}
{{--            <img src="{{ asset('images/Logo.png') }}" alt="{{ config('app.name') }}" class="brand-logo">--}}
{{--            <span class="brand-text">{{ config('app.name') }}</span>--}}
{{--        </a>--}}

{{--        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">--}}
{{--            <span class="toggler-icon"><i class="fa-solid fa-ellipsis-vertical"></i></span>--}}
{{--        </button>--}}

{{--        <div id="navMenu" class="collapse navbar-collapse">--}}
{{--            <ul class="navbar-nav ms-auto align-items-lg-center">--}}
{{--                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>--}}
{{--                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>--}}
{{--                <li class="nav-item"><a class="nav-link" href="#gallery">Gallery</a></li>--}}
{{--                <li class="nav-item"><a class="nav-link" href="#reviews">Reviews</a></li>--}}
{{--                <li class="nav-item">--}}
{{--                    <a class="btn btn-warning text-dark px-4 ms-lg-3" href="#contact">--}}
{{--                        Contact--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</nav>--}}
@include('partials.sections.nav')

<!-- Hero Section -->
<section id="hero" class="hero-section">
    <video class="hero-video" autoplay muted loop playsinline poster="{{ asset('home/images/banner.jpg') }}">
        <source src="{{ asset('home/videos/bg.mp4') }}" type="video/mp4">
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/common-interactions.js') }}"></script>
</body>
</html>
