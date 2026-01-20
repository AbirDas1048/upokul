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
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }
        a {
            text-decoration: none;
        }
        /* Navbar */
        .navbar {
            padding: 1rem 2rem;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .nav-link {
            font-weight: 500;
        }
        /* Hero Section */
        #hero {
            background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1650&q=80') no-repeat center center;
            background-size: cover;
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        #hero .overlay {
            background: rgba(0,0,0,0.5);
            height: 100%;
            width: 100%;
            position: absolute;
            top:0; left:0;
        }
        #hero .hero-content {
            position: relative;
            z-index: 2;
        }
        #hero h1 {
            font-size: 3rem;
            font-weight: 700;
        }
        #hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        /* About Section */
        #about {
            padding: 100px 0;
        }
        #about img {
            border-radius: 20px;
        }
        /* Services */
        #services {
            padding: 100px 0;
            background-color: #f9f9f9;
        }
        .service-card {
            border: none;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            background-color: #fff;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .service-card i {
            font-size: 3rem;
            color: #ff6b6b;
            margin-bottom: 15px;
        }
        /* Gallery */
        #gallery {
            padding: 100px 0;
        }
        #gallery img {
            border-radius: 15px;
            transition: transform 0.3s;
        }
        #gallery img:hover {
            transform: scale(1.05);
        }
        /* Reviews */
        #reviews {
            padding: 100px 0;
            background-color: #f9f9f9;
        }
        .review-card {
            border: none;
            border-radius: 15px;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .review-card:hover {
            transform: translateY(-5px);
        }
        .review-card img {
            border-radius: 50%;
            width: 60px;
            margin-right: 15px;
        }
        /* Contact */
        #contact {
            padding: 100px 0;
        }
        /* Footer */
        footer {
            background-color: #333;
            color: #fff;
            padding: 40px 0;
        }
        footer a {
            color: #ff6b6b;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">TravelNow</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#hero">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#gallery">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="#reviews">Reviews</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="hero" class="d-flex">
    <div class="overlay"></div>
    <div class="container hero-content text-center text-md-start">
        <h1>Explore the World with TravelNow</h1>
        <p>Your adventure starts here. Discover amazing destinations and unforgettable experiences.</p>
        <a href="#services" class="btn btn-primary btn-lg">Discover More</a>
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
</script>
</body>
</html>
