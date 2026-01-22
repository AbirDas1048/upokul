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


    <style>
        :root { --accent-green: #20c997; }

        /* --- Global Responsive Typography --- */
        html { font-size: 16px; }
        @media (min-width: 1400px) { html { font-size: 17px; } }
        @media (max-width: 991.98px) { html { font-size: 15px; } }
        @media (max-width: 575.98px) { html { font-size: 14px; } }

        html, body { overflow-x: hidden; }
        body { font-family: 'Poppins', sans-serif; scroll-behavior: smooth; }
        a { text-decoration: none; }

        /* --- Navbar --- */
        #mainNav {
            background: rgba(0,0,0,0.85);
            transform: translateY(-100%);
            transition: all 0.4s ease;
            z-index: 999;
        }
        #mainNav.show { transform: translateY(0); }

        .navbar { padding: 1rem 2rem; }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; }
        .nav-link { font-weight: 500; }
        #mainNav .nav-link:hover { color: var(--accent-green); }

        #navMenu .nav-item { margin: 0.5rem 0; }
        #navMenu .btn { margin-top: 0.5rem; }

        /* --- Hero Section --- */
        .hero-section {
            position: relative;
            min-height: 100svh;
            min-height: 100vh;
            overflow: hidden;

            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* text sits at top */
        }



        .hero-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.45) 50%, rgba(0,0,0,0.2) 100%);
        }

        .hero-section .container {
            position: relative;
            z-index: 2;
            padding-top: 5rem;   /* pushes text down from top */
            padding-bottom: 3rem; /* space below buttons */
        }

        .hero-content {
            max-width: 600px;
            color: #fff;
        }

        .hero-content h1 {
            font-size: 3.4rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem; /* space after heading */
        }

        .hero-content h1 span {
            background: linear-gradient(90deg, #ffc107, var(--accent-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            margin-bottom: 2rem; /* space before buttons */
            font-size: 1.1rem;
        }

        /* Hero Buttons */
        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-glossy-primary,
        .btn-glossy-secondary {
            padding: 0.875rem 1.875rem;
            border-radius: 50px;
            font-weight: 600;
            color: #fff; /* text color */
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-glossy-primary {
            background: linear-gradient(135deg, #ff9f00, #ffc107);
            box-shadow: 0 12px 30px rgba(255,193,7,0.45);
        }

        .btn-glossy-secondary {
            background: linear-gradient(135deg, #0dcaf0, #0d6efd);
            box-shadow: 0 12px 30px rgba(13,110,253,0.45);
        }

        .btn-glossy-primary i,
        .btn-glossy-secondary i {
            color: #fff; /* force icon color */
            margin-right: 0.5rem;                  /* spacing from text */
            font-size: 1.1rem;                     /* optional: slightly bigger icon */
        }

        .btn-glossy-primary:hover,
        .btn-glossy-secondary:hover {
            transform: translateY(-4px);
            color: #fff; /* keeps text white on hover */
        }

        /* Medium devices (tablets) */
        @media (max-width: 991.98px) {
            .hero-section {
                min-height: auto; /* let content define height */
            }
            .hero-section .container {
                padding-top: 4rem;
                padding-bottom: 1rem; /* less space below buttons */
            }
        }



        /* Mobile */
        @media (max-width: 768px) {
            .hero-section {
                min-height: auto; /* let content determine height */
            }
            .hero-section .container { padding-top: 4rem; padding-bottom: 1rem; }
            .hero-content { text-align: center; }
            .hero-buttons a {
                width: 100%;
                justify-content: center;
                margin-bottom: 0; /* remove any extra margin from buttons */
            }
        }
    </style>
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
    <video class="hero-video" autoplay muted loop playsinline poster="{{ asset('images/hero-poster.jpg') }}">
        <source src="{{ asset('videos/bg.mp4') }}" type="video/mp4">
    </video>

    <div class="hero-overlay"></div>

    <div class="container">
        <div class="row">
            <div class="col-lg-6 hero-content">
                <h1>
                    Discover Beautiful Places <br>
                    <span>With Upokul Travels</span>
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
