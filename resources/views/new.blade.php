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
</head>

<body>

<!-- Navbar -->
<nav id="mainNav" class="navbar navbar-expand-lg fixed-top navbar-dark">
    <div class="container">
        {{--        <a class="navbar-brand fw-bold" href="#hero">{{ config('app.name') }}</a>--}}
        <a class="navbar-brand fw-bold" href="#hero">
            <img src="{{ asset('images/Logo.png') }}" alt="{{ config('app.name') }}" class="brand-logo">
            <span class="brand-text">{{ config('app.name') }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="toggler-icon"><i class="fa-solid fa-ellipsis-vertical"></i></span>
        </button>

        <div id="navMenu" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#gallery">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="#reviews">Reviews</a></li>
                <li class="nav-item">
                    <a class="btn btn-warning text-dark px-4 ms-lg-3" href="#contact">
                        Contact
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

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
@include('partials.sections.about', ['variant' => 'classic'])
<!-- Services Section -->
@include('partials.sections.services', ['variant' => 'classic'])

<!-- Counter Section -->
@include('partials.sections.trusted', ['variant' => 'classic'])

<!-- Gallery Section -->
@include('partials.sections.gallery', ['variant' => 'classic'])

<section id="reviews" class="reviews-section">
    <div class="container">

        <div class="section-header text-center">
            <h2 class="premium-section-title">What Travelers Say</h2>
            <p class="premium-section-subtitle">Real experiences from our Google Business Profile</p>
        </div>

        <div class="swiper reviewSwiper swiper-premium">
            <div class="swiper-wrapper">

                <!-- Review Card -->
                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p>
                            Amazing service! Visa processing and hotel booking
                            were handled smoothly. Highly recommended.
                        </p>
                        <div class="review-author">
                            <strong>Rahim Ahmed</strong>
                            <span class="google-badge">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google">
                                    Google Review
                                </span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p>
                            Very professional team. Our family tour was perfectly
                            planned and stress-free.
                        </p>
                        <div class="review-author">
                            <strong>Nusrat Jahan</strong>
                            <span class="google-badge">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google">
                                    Google Review
                                </span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p>
                            Best travel agency in Bangladesh. Quick support and
                            transparent pricing.
                        </p>
                        <div class="review-author">
                            <strong>Tanvir Hasan</strong>
                            <span class="google-badge">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google">
                                    Google Review
                                </span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p>
                            Amazing service! Visa processing and hotel booking
                            were handled smoothly. Highly recommended.
                        </p>
                        <div class="review-author">
                            <strong>Rahim Ahmed</strong>
                            <span class="google-badge">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/1b/Facebook_icon.svg" alt="Facebook">
                                    Facebook Review
                                </span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p>
                            Very professional team. Our family tour was perfectly
                            planned and stress-free.
                        </p>
                        <div class="review-author">
                            <strong>Nusrat Jahan</strong>
                            <span class="google-badge">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/1b/Facebook_icon.svg" alt="Facebook">
                                    Facebook Review
                                </span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p>
                            Best travel agency in Bangladesh. Quick support and
                            transparent pricing.
                        </p>
                        <div class="review-author">
                            <strong>Tanvir Hasan</strong>
                            <span class="google-badge">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/1b/Facebook_icon.svg" alt="Facebook">
                                    Facebook Review
                                </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>

        <div class="review-actions mt-5 text-center">
            <a href="https://search.google.com/local/writereview?placeid=ChIJb5Q_kd_ZrDARzdblKHT4xYo"
               target="_blank" class="btn premium-btn premium-btn-pill premium-btn-glossy-google">
                <i class="fab fa-google"></i>
                Google Reviews
            </a>

            <a href="https://www.facebook.com/profile.php?id=61555938637948&sk=reviews" target="_blank"
               class="btn premium-btn premium-btn-pill premium-btn-glossy-facebook">
                <i class="fab fa-facebook-f"></i>
                Facebook Reviews
            </a>
        </div>

    </div>
</section>


<!-- Contact Section -->
<section id="contact" class="contact-section">
    <div class="container">

        <!-- Section Header -->
        <div class="section-header text-center">
            <h2 class="premium-section-title">Contact Us</h2>
            <p class="premium-section-subtitle">We’d love to hear from you. Get in touch anytime.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        <div class="contact-grid">

            <!-- Contact Form -->
            <div class="contact-card">
                <h4>Send a Message</h4>
                <form id="contactForm" method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" id="name" placeholder="Your Name *" required>
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" id="email" placeholder="Your Email *" required>
                    </div>

                    <div class="form-group">
                        <input type="text" name="phone" id="phone" placeholder="Phone Number *" required>
                    </div>

                    <div class="form-group">
                        <input type="text" name="subject" id="subject" placeholder="Subject *" required>
                    </div>

                    <div class="form-group">
                        <textarea rows="4" name="mail_message" id="mail_message" placeholder="Your Message *" required></textarea>
                    </div>

                    <button type="submit" class="premium-btn premium-btn-pill premium-btn-block premium-btn-solid" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <!-- Map + Info -->
            <div class="contact-card info-card">

                <!-- Google Map -->
                <div class="map-box">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3692.600173850267!2d91.81103287529328!3d22.325913979668194!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30acd9df913f946f%3A0x8ac5f87428e5d6cd!2sUpokul%20Travels%20%26%20Holidays!5e0!3m2!1sen!2sbd!4v1768747684228!5m2!1sen!2sbd"
                        title="Upokul Travels location map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen>
                    </iframe>
                </div>

                <!-- Company Info -->
                <div class="company-info">
                    <h4>{{ config('app.name') }}</h4>

                    <p>
                        <i class="fa-solid fa-location-dot"></i>
                        {{ config('custom.office_address') }} <br>

                        <i class="fa-solid fa-envelope"></i>
                        {{ config('custom.office_email') }} <br>

                        <i class="fa-solid fa-phone"></i>
                        {{ config('custom.office_phone') }}
                    </p>
                </div>

                <!-- Owner Info -->
                <div class="owner-box">
                    <img src="{{ asset('images/avatar.png') }}" alt="Owner">

                    <div>
                        <strong>
                            <i class="fa-solid fa-user-tie"></i>
                            {{ config('custom.owner_designation') }}:
                            Mr. {{ config('custom.owner_name') }}
                        </strong>

                        <span>
                                <i class="fa-solid fa-envelope"></i>
                                {{ config('custom.owner_email') }}
                            </span>

                        <span>
                                <i class="fa-solid fa-phone"></i>
                                {{ config('custom.owner_phone') }}
                            </span>
                    </div>
                </div>


            </div>

        </div>
    </div>
</section>

<!-- Footer -->
<div class="footer-bottom text-center py-4">
    <div class="container">

        <!-- Anchor Links -->
        <div class="footer-anchor-links mb-2">
            <a href="#about">About</a>
            <a href="#services">Services</a>
            <a href="#gallery">Gallery</a>
            <a href="#contact">Contact</a>
        </div>

        <!-- Copyright -->
        <small class="footer-copy">
            © {{ date('Y') }} {{ config('app.name') }}.
            All Rights Reserved.
        </small>

    </div>
</div>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/{{config('custom.whatsapp_number')}}" target="_blank" class="whatsapp-float" aria-label="Chat on WhatsApp">
    <span class="wa-label">Chat with us!</span>
    <span class="wa-icon">
        <i class="fab fa-whatsapp"></i>
    </span>
</a>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/common-interactions.js') }}"></script>
</body>

</html>
