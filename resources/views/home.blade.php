<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href=" {{ asset('home/images/favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <!-- AOS Library CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <!-- Swiper Library CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('home/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/nav.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/service.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/counter.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/gallery.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/review.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/contact.css') }}">
</head>
<body>

<!-- Navbar -->
<nav id="mainNav" class="navbar navbar-expand-lg fixed-top navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Upokul Travels</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
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
<section class="hero-section">
    <video class="hero-video" autoplay muted loop playsinline poster="{{ asset('home/images/banner.jpg') }}">
        <source src="{{ asset('home/videos/bg.mp4') }}" type="video/mp4">
    </video>

    <div class="hero-overlay"></div>

    <div class="container">
        <div class="row">
            <div class="col-lg-6 hero-content">
                <h1>
                    Discover Beautiful Places <br>
                    <span>With {{config('app.name')}}</span>
                </h1>

                <p>
                    We create unforgettable travel experiences with comfort,
                    safety and trusted service across Bangladesh & beyond.
                </p>

                <div class="hero-buttons">
                    <a href="#services" class="btn btn-glossy-primary">
                        <i class="fas fa-route"></i> Our Services
                    </a>
                    <a href="#gallery" class="btn btn-glossy-secondary">
                        <i class="fas fa-headset"></i> Explore Places
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about-section">
    <div class="container">
        <div class="row align-items-center gy-5">

            <!-- Image -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-image-wrapper" >
                    <img src="{{ asset('images/Logo.png') }}" alt="About Upokul Travels">
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-6" data-aos="fade-light">
                <div class="about-content">
                    <h4 class="about-badge">About Us</h4>

                    <h2>
                        Your Trusted Partner for
                        <span>Unforgettable Journeys</span>
                    </h2>

                    <p>
                        Upokul Travels is dedicated to creating meaningful travel experiences.
                        From breathtaking coastal tours to personalized holiday packages,
                        we ensure comfort, safety, and unforgettable memories.
                    </p>

                    <ul class="about-list">
                        <li><i class="fas fa-check-circle"></i> Trusted & experienced travel experts</li>
                        <li><i class="fas fa-check-circle"></i> Customized tour packages</li>
                        <li><i class="fas fa-check-circle"></i> 24/7 customer support</li>
                    </ul>

                    <a href="#trusted" class="btn btn-glossy-primary mt-3" data-aos="fade-up">
                        Why Choice Us?
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="services-section">
    <div class="container">

        <!-- Section Title -->
        <div class="text-center mb-5">
            <span class="section-badge" data-aos="fade-up">Our Services</span>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">
                Everything You Need for a
                <span>Perfect Journey</span>
            </h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">
                We provide complete travel solutions with comfort, reliability,
                and personalized care.
            </p>
        </div>

        <!-- Services Grid -->
        <div class="row g-4">

            <!-- Service Card -->
            <div class="col-xl-4 col-md-6" data-aos="fade-up">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h5>Tour Planning</h5>
                    <p>
                        Customized tour plans designed to give you unforgettable
                        experiences and hassle-free journeys.
                    </p>
                </div>
            </div>

            <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <h5>Hotel Booking</h5>
                    <p>
                        Hand-picked hotels at the best prices ensuring comfort,
                        safety, and quality stays.
                    </p>
                </div>
            </div>

            <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                    <h5>Air Ticket Booking</h5>
                    <p>
                        Affordable and flexible flight bookings with trusted
                        airlines worldwide.
                    </p>
                </div>
            </div>

            <div class="col-xl-4 col-md-6" data-aos="fade-up">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-passport"></i>
                    </div>
                    <h5>Visa Processing</h5>
                    <p>
                        Smooth and reliable visa processing with expert guidance
                        at every step.
                    </p>
                </div>
            </div>

            <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h5>Transport & Transfer</h5>
                    <p>
                        Comfortable and safe transport solutions for airport
                        transfers and tours.
                    </p>
                </div>
            </div>

            <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Travel Insurance</h5>
                    <p>
                        Secure your journey with reliable travel insurance
                        coverage and peace of mind.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Counter Section -->
<section class="trust-section" id="trusted">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title" data-aos="fade-up">
                Trusted by Travelers Worldwide
            </h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="150">
                Numbers that reflect our experience, trust, and global reach
            </p>
        </div>

        <div class="row g-4">
            <!-- Item -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up">
                <div class="trust-card">
                    <i class="fas fa-smile-beam"></i>
                    <h3 class="counter" data-count="100">0</h3>
                    <p>Satisfied Clients</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="trust-card">
                    <i class="fas fa-globe-asia"></i>
                    <h3 class="counter" data-count="300">0</h3>
                    <p>Countries Connected</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="trust-card">
                    <i class="fas fa-hotel"></i>
                    <h3 class="counter" data-count="800">0</h3>
                    <p>Hotel Partnerships</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="trust-card">
                    <i class="fas fa-handshake"></i>
                    <h3 class="counter" data-count="500">0</h3>
                    <p>Trusted Agents & Media</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Gallery Section -->
<section id="gallery" class="gallery-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Travel Moments</h2>
            <p class="text-muted">Explore our unforgettable journeys</p>
        </div>

        <!-- CARD SLIDER -->
        <div class="swiper cardSwiper">
            <div class="swiper-wrapper">

                <!-- Card -->
                <div class="swiper-slide">
                    <div class="gallery-card" data-index="0">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Cox’s Bazar</h5>
                            <span>Bangladesh</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="1">
                        <img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Sylhet</h5>
                            <span>Bangladesh</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="2">
                        <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Bali</h5>
                            <span>Indonesia</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="0">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Cox’s Bazar</h5>
                            <span>Bangladesh</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="1">
                        <img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Sylhet</h5>
                            <span>Bangladesh</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="2">
                        <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Bali</h5>
                            <span>Indonesia</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pagination BELOW slider -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- FULLSCREEN MODAL -->
<div class="fullscreen-modal">
    <div class="swiper fullscreenSwiper">
        <div class="swiper-wrapper">

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=2000" alt="">
            </div>

        </div>

        <!-- Arrows -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        <!-- Close -->
        <button class="close-modal">&times;</button>
    </div>
</div>


<!-- Reviews Section -->
<section id="reviews" class="reviews-section">
    <div class="container">

        <div class="section-header text-center">
            <h2>What Travelers Say</h2>
            <p>Real experiences from our Google Business Profile</p>
        </div>

        <div class="swiper reviewSwiper">
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
               target="_blank"
               class="btn btn-glossy-google">
                <i class="fab fa-google"></i>
                Google Reviews
            </a>

            <a href="https://www.facebook.com/profile.php?id=61555938637948&sk=reviews"
               target="_blank"
               class="btn btn-glossy-facebook">
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
            <h2>Contact Us</h2>
            <p>We’d love to hear from you. Get in touch anytime.</p>
        </div>

        <div class="contact-grid">

            <!-- Contact Form -->
            <div class="contact-card">
                <h4>Send a Message</h4>
                <form>
                    <div class="form-group">
                        <input type="text" placeholder="Your Name" required>
                    </div>

                    <div class="form-group">
                        <input type="email" placeholder="Your Email" required>
                    </div>

                    <div class="form-group">
                        <input type="text" placeholder="Phone Number">
                    </div>

                    <div class="form-group">
                        <textarea rows="4" placeholder="Your Message" required></textarea>
                    </div>

                    <button type="submit" class="btn-primary">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Map + Info -->
            <div class="contact-card info-card">

                <!-- Google Map -->
                <div class="map-box">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3692.600173850267!2d91.81103287529328!3d22.325913979668194!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30acd9df913f946f%3A0x8ac5f87428e5d6cd!2sUpokul%20Travels%20%26%20Holidays!5e0!3m2!1sen!2sbd!4v1768747684228!5m2!1sen!2sbd"
                        loading="lazy"
                        allowfullscreen>
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
<footer class="text-center">
    <div class="container">
        <p>&copy; 2026 TravelNow. All rights reserved.</p>
        <div>
            <a href="https://www.facebook.com/profile.php?id=61555938637948" target="_blank"><i class="fab fa-facebook fa-lg me-3"></i></a>
            <a href="#"><i class="fab fa-twitter fa-lg me-3"></i></a>
            <a href="https://www.instagram.com/upokul80/" target="_blank"><i class="fab fa-instagram fa-lg"></i></a>
        </div>
    </div>
</footer>

<!-- jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    /* ===============================
       NAV SHOW / HIDE ON SCROLL
    ================================ */
    $(window).on('scroll', function () {
        const heroHeight = $('#hero').outerHeight();

        if ($(window).scrollTop() > heroHeight - 120) {
            $('#mainNav').addClass('show');
        } else {
            $('#mainNav').removeClass('show');
        }
    });

    /* ===============================
       AOS INIT
    ================================ */
    AOS.init({
        duration: 1000,
        once: true,
        easing: 'ease-out-cubic'
    });

    /* ===============================
       PREMIUM SMOOTH SCROLL
    ================================ */
    (function () {
        const easeInOutCubic = t =>
            t < 0.5
                ? 4 * t * t * t
                : 1 - Math.pow(-2 * t + 2, 3) / 2;

        function smoothScrollTo(targetY, duration = 1400) {
            const startY = window.pageYOffset;
            const diff = targetY - startY;
            let startTime = null;

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                const time = timestamp - startTime;
                const progress = Math.min(time / duration, 1);
                const eased = easeInOutCubic(progress);

                window.scrollTo(0, startY + diff * eased);

                if (time < duration) {
                    requestAnimationFrame(step);
                }
            }

            requestAnimationFrame(step);
        }

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const id = this.getAttribute('href');
                if (!id || id === '#') return;

                const target = document.querySelector(id);
                if (!target) return;

                e.preventDefault();

                const navHeight =
                    document.getElementById('mainNav')?.offsetHeight || 90;

                const targetY =
                    target.getBoundingClientRect().top +
                    window.pageYOffset -
                    navHeight;

                smoothScrollTo(targetY, 1500); // 🎩 premium timing
            });
        });
    })();

    /* ===============================
   COUNTER ANIMATION (FIXED)
================================ */

    let counterStarted = false;

    function animateCounter(el, target) {
        let current = 0;
        const increment = Math.max(1, Math.ceil(target / 80));

        const interval = setInterval(() => {
            current += increment;

            if (current >= target) {
                el.textContent = target;
                clearInterval(interval);
            } else {
                el.textContent = current;
            }
        }, 25);
    }

    const trustedSection = document.querySelector('#trusted');

    if (trustedSection) {
        const observer = new IntersectionObserver(
            entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !counterStarted) {
                        counterStarted = true;

                        document.querySelectorAll('.counter').forEach(counter => {
                            animateCounter(counter, Number(counter.dataset.count));
                        });

                        observer.unobserve(entry.target); // safer than disconnect
                    }
                });
            },
            {
                threshold: 0.15,                 // 🔑 mobile-friendly
                rootMargin: '0px 0px -80px 0px'   // 🔑 triggers earlier
            }
        );

        observer.observe(trustedSection);
    }

    /* Card slider */
    const cardSwiper = new Swiper('.cardSwiper', {
        slidesPerView: 1,
        spaceBetween: 24,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        breakpoints: {
            768: { slidesPerView: 2 },
            1200: { slidesPerView: 3 }
        }
    });

    /* Fullscreen slider */
    const fullscreenSwiper = new Swiper('.fullscreenSwiper', {
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        loop: true
    });

    /* Open modal */
    document.querySelectorAll('.gallery-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelector('.fullscreen-modal').classList.add('active');
            fullscreenSwiper.slideToLoop(parseInt(card.dataset.index), 0);
        });
    });

    /* Close modal */
    document.querySelector('.close-modal').addEventListener('click', () => {
        document.querySelector('.fullscreen-modal').classList.remove('active');
    });
</script>

<script>
    const reviewSwiper = new Swiper('.reviewSwiper', {
        slidesPerView: 1,
        spaceBetween: 24,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        breakpoints: {
            768: {
                slidesPerView: 2
            },
            1200: {
                slidesPerView: 3
            }
        }
    });
</script>


</body>
</html>
