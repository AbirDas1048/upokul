<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Agency Landing Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- AOS Library CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('home/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/nav.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/service.css') }}">
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
                    <a href="#contact" class="btn btn-glossy-secondary">
                        <i class="fas fa-headset"></i> Get Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

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

                    <a href="#services" class="btn btn-glossy-primary mt-3" data-aos="fade-up">
                        Explore Our Services
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


<!-- Gallery Section -->
<section id="gallery" class="container">
    <div class="text-center mb-5">
        <h2>Gallery</h2>
        <p>Explore some of our most memorable trips and destinations.</p>
    </div>
    <div class="row g-3">
        <div class="col-md-4"><img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80" class="img-fluid" alt="Gallery 1"></div>
        <div class="col-md-4"><img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?auto=format&fit=crop&w=800&q=80" class="img-fluid" alt="Gallery 2"></div>
        <div class="col-md-4"><img src="https://images.unsplash.com/photo-1660703080906-f4ac0cb7ea43?auto=format&fit=crop&w=800&q=80" class="img-fluid" alt="Gallery 3"></div>
    </div>
</section>

<!-- Reviews Section -->
<section id="reviews" class="container">
    <div class="text-center mb-5">
        <h2>What Our Clients Say</h2>
        <p>Testimonials from our happy travelers</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="review-card d-flex align-items-center">
                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Client">
                <div>
                    <h5>Jane Doe</h5>
                    <p>"Amazing travel experience! Everything was perfectly organized."</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="review-card d-flex align-items-center">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Client">
                <div>
                    <h5>John Smith</h5>
                    <p>"Highly recommend TravelNow. The tours are exciting and hassle-free."</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="review-card d-flex align-items-center">
                <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Client">
                <div>
                    <h5>Emily Brown</h5>
                    <p>"Great service, professional staff, and wonderful destinations!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="container">
    <div class="text-center mb-5">
        <h2>Contact Us</h2>
        <p>Get in touch for your next adventure</p>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form>
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Your Name">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Your Email">
                </div>
                <div class="mb-3">
                    <textarea class="form-control" rows="5" placeholder="Your Message"></textarea>
                </div>
                <button class="btn btn-primary w-100">Send Message</button>
            </form>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="text-center">
    <div class="container">
        <p>&copy; 2026 TravelNow. All rights reserved.</p>
        <div>
            <a href="#"><i class="fab fa-facebook fa-lg me-3"></i></a>
            <a href="#"><i class="fab fa-twitter fa-lg me-3"></i></a>
            <a href="#"><i class="fab fa-instagram fa-lg"></i></a>
        </div>
    </div>
</footer>

<!-- jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
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
</script>
</body>
</html>
