<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- AOS Library CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->


    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('css/review.css') }}">
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/whatsapp.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top" data-aos="fade-down">
    <div class="container">
        <a class="navbar-brand" href="#">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#reviews">Reviews</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->

<section class="hero">
    <div class="hero-content text-center">
        <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100">Explore the World with {{config('app.name')}}</h1>
        <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="300">Your journey begins here — discover amazing tours and experiences.</p>
        <a href="#services" class="btn btn-primary hero-btn" data-aos="fade-up" data-aos-delay="500">Our Services</a>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-md-6" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80" class="img-fluid" alt="About Us">
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <h2>About Upokul Travels</h2>
                <p>Upokul Travels provides seamless travel experiences, from travel planning, air ticket booking to visa processing. Our team ensures every journey is memorable and hassle-free.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->

<!-- Services Section -->
<section id="services">
    <div class="container">
        <div class="section-title">
            <h2>Our Services</h2>
            <p>Complete travel solutions under one roof</p>
        </div>

        <div class="row g-4">

            <!-- Travel Planning -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="service-card">
                    <div class="card-icon">
                        <img src="https://img.icons8.com/ios-filled/50/around-the-globe.png" alt="Travel Planning">
                    </div>
                    <h5>Travel Planning</h5>
                    <p>Customized itineraries designed to fit your budget and schedule.</p>
                </div>
            </div>

            <!-- Air Ticket Booking -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card">
                    <div class="card-icon">
                        <img src="https://img.icons8.com/ios-filled/50/airplane-take-off.png" alt="Air Ticket Booking">
                    </div>
                    <h5>Air Ticket Booking</h5>
                    <p>Affordable domestic & international flight ticket booking.</p>
                </div>
            </div>

            <!-- Visa Processing -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card">
                    <div class="card-icon">
                        <img src="https://img.icons8.com/ios-filled/50/visa.png" alt="Visa Processing">
                    </div>
                    <h5>Visa Processing</h5>
                    <p>Professional visa assistance for smooth international travel.</p>
                </div>
            </div>

            <!-- Travel Insurance -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="service-card">
                    <div class="card-icon">
                        <img src="https://img.icons8.com/ios-filled/50/medical-insurance.png" alt="Travel Insurance">
                    </div>
                    <h5>Travel Insurance</h5>
                    <p>Comprehensive coverage for medical emergencies & trip delays.</p>
                </div>
            </div>

            <!-- Hotel Booking -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card">
                    <div class="card-icon">
                        <img src="https://img.icons8.com/ios-filled/50/5-star-hotel.png" alt="Hotel Booking">
                    </div>
                    <h5>Hotel Booking</h5>
                    <p>Best hotel deals worldwide for comfort and affordability.</p>
                </div>
            </div>

            <!-- Transport Facilities -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card">
                    <div class="card-icon">
                        <img src="https://img.icons8.com/ios-filled/50/bus.png" alt="Transport Facilities">
                    </div>
                    <h5>Transport Facilities</h5>
                    <p>Reliable transport options including cars, buses & transfers.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="reviews" class="section-soft">
    <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up">What Our Clients Say</h2>

        <!-- Elfsight Reviews Widget -->
        <script src="https://elfsightcdn.com/platform.js" async></script>
        <div class="elfsight-app-57ae0b6d-211e-469f-b760-b6058da871ac" data-elfsight-app-lazy></div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact-section">
    <div class="container">

        <div class="row g-4 align-items-stretch">

            <!-- LEFT COLUMN -->
            <div class="col-lg-6 d-flex flex-column gap-4">

                <!-- Card 1: Chairman & Office -->
                <div class="contact-card info-card h-100" data-aos="fade-right">
                    <div class="chairman-box">
                        <img src="{{asset('images/avatar.png')}}" alt="Chairman">
                        <div>
                            <h5>Debraj Mitra</h5>
                            <span>Owner, {{config('app.name')}}</span>
                        </div>
                    </div>

                    <ul class="contact-info mt-4">
                        <li>📍 123, Motijheel, Dhaka, Bangladesh</li>
                    </ul>
                </div>

                <!-- Card 2: Quick Contact -->
                <div class="contact-card info-card h-100" data-aos="fade-right" data-aos-delay="150">
                    <h5 class="mb-3">Quick Contact</h5>
                    <ul class="contact-info">
                        <li>📞 <a href="tel:+880123456789">+880 1234-56789</a></li>
                        <li>✉️ <a href="mailto:info@upokultravels.com">info@upokultravels.com</a></li>
                        <li>
                            🔗 <a href="https://facebook.com/upokultravels" target="_blank">
                                Facebook Page
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- RIGHT COLUMN: Contact Form -->
            <div class="col-lg-6 d-flex" data-aos="fade-left">
                <div class="contact-card form-card w-100">
                    <h4>Get In Touch</h4>
                    <form>
                        <input type="text" class="form-control" placeholder="Your Name">
                        <input type="email" class="form-control" placeholder="Your Email">
                        <input type="tel" class="form-control" placeholder="Phone Number">
                        <textarea class="form-control" rows="4" placeholder="Your Message"></textarea>
                        <button type="submit" class="btn btn-submit">Send Message</button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Google Map -->
        <div class="map-box mt-5" data-aos="fade-up">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3692.600173850267!2d91.81103287529328!3d22.325913979668194!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30acd9df913f946f%3A0x8ac5f87428e5d6cd!2sUpokul%20Travels%20%26%20Holidays!5e0!3m2!1sen!2sbd!4v1768747684228!5m2!1sen!2sbd"
                loading="lazy">
            </iframe>
        </div>

    </div>
</section>

<footer class="simple-footer">
    <p>© <span id="year"></span> Upokul Travels. All rights reserved.</p>
</footer>

<!-- WhatsApp Button -->
<a href="https://wa.me/+8801868330080" target="_blank" class="whatsapp-btn">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true
    });


    document.getElementById("year").textContent = new Date().getFullYear();
</script>
</body>
</html>
