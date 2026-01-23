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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('home/css/style.css') }}">
{{--    <link rel="stylesheet" href="{{ asset('home/css/style2.css') }}">--}}
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

{{--<section class="hero-section">--}}
{{--    <!-- Video Background -->--}}
{{--    <video class="hero-video" autoplay muted loop playsinline poster="{{ asset('home/images/banner.jpg') }}">--}}
{{--        <source src="{{ asset('home/videos/bg.mp4') }}" type="video/mp4">--}}
{{--    </video>--}}

{{--    <!-- Overlay -->--}}
{{--    <div class="hero-overlay"></div>--}}

{{--    <!-- Content -->--}}
{{--    <div class="container hero-content">--}}
{{--        <div class="row">--}}
{{--            <div class="col-xl-6 col-lg-7 col-md-9">--}}
{{--                <h1 class="hero-title">--}}
{{--                    Discover Beautiful Places--}}
{{--                </h1>--}}

{{--                <p class="hero-text">--}}
{{--                    Explore breathtaking destinations with comfort, style, and unforgettable travel experiences.--}}
{{--                </p>--}}

{{--                <div class="hero-buttons d-flex flex-wrap gap-3">--}}
{{--                    <a href="#" class="btn btn-primary btn-lg">--}}
{{--                        Explore Tours--}}
{{--                    </a>--}}
{{--                    <a href="#" class="btn btn-outline-light btn-lg">--}}
{{--                        Contact Us--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}



<!-- About Section -->
<section id="about" class="container">
    <div class="row align-items-center">
        <div class="col-md-6 mb-4">
            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80" alt="About Travel" class="img-fluid">
        </div>
        <div class="col-md-6">
            <h2>About TravelNow</h2>
            <p>We are a premier travel agency dedicated to crafting unforgettable journeys for our clients. From exotic vacations to local adventures, we make every trip seamless and memorable.</p>
            <ul>
                <li>Custom travel packages</li>
                <li>Expert travel guidance</li>
                <li>24/7 customer support</li>
            </ul>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="container">
    <div class="text-center mb-5">
        <h2>Our Services</h2>
        <p>We provide a variety of travel services to make your journey comfortable and unforgettable.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="service-card">
                <i class="fas fa-plane"></i>
                <h4>Flight Booking</h4>
                <p>Book flights easily to any destination worldwide with best deals.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card">
                <i class="fas fa-hotel"></i>
                <h4>Hotel Reservations</h4>
                <p>Stay in premium hotels and resorts with our special partnerships.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card">
                <i class="fas fa-map"></i>
                <h4>Custom Tours</h4>
                <p>Plan tailor-made tours and travel experiences with our experts.</p>
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
        <div class="col-md-4"><img src="https://images.unsplash.com/photo-1549887534-27e65cbbf379?auto=format&fit=crop&w=800&q=80" class="img-fluid" alt="Gallery 3"></div>
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
<script>
    // Smooth scrolling for nav links
    $(document).ready(function(){
        $('a.nav-link').click(function(e){
            e.preventDefault();
            var target = $(this).attr('href');
            $('html, body').animate({
                scrollTop: $(target).offset().top - 70
            }, 800);
        });
    });

    $(window).on('scroll', function () {
        const heroHeight = $('#hero').outerHeight();

        if ($(window).scrollTop() > heroHeight - 120) {
            $('#mainNav').addClass('show');
        } else {
            $('#mainNav').removeClass('show');
        }
    });

</script>
</body>
</html>
