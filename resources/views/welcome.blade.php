<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upokul Travels</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- AOS Library CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #025d61;
            --secondary-color: #35b1d4;
            --bg-light: #f8f9fa;
        }

        body { font-family: 'Roboto', sans-serif; scroll-behavior: smooth; }

        /* Navbar */
        .navbar { padding: 1rem 2rem; }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; color: var(--primary-color); }
        .navbar-nav .nav-link { margin-right: 1rem; font-weight: 500; color: #555; }
        .navbar-nav .nav-link:hover { color: var(--primary-color); }

        /* Hero Section */
        .hero {
            background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            position: relative;
        }
        .hero::after {
            content: "";
            position: absolute;
            top:0; left:0;
            width:100%; height:100%;
            background-color: rgba(0,0,0,0.5);
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero h1, .hero p, .hero .btn {
            opacity: 0; /* Start invisible for AOS fade-in */
        }
        .hero .btn-primary {
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 500;
            background-color: #025d61;
            border: none;
            transition: transform 0.3s;
        }
        .hero .btn-primary:hover {
            background-color: #35b1d4;
            transform: scale(1.05);
        }
        /* About Section */
        .about-section { padding: 80px 0; background-color: var(--bg-light); }
        .about-section img { border-radius: 15px; }

        /* Service Section */
        .service-section { padding: 60px 0; }
        .service-card {
            border: none; border-radius: 20px; padding: 30px 20px; color: #fff; text-align: center; position: relative; overflow: hidden;
            height: 100%; display: flex; flex-direction: column; justify-content: space-between;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            transition: transform 0.5s, box-shadow 0.5s, background 0.5s;
        }
        .service-card::before {
            content: ""; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(45deg); opacity: 0; transition: opacity 0.5s; pointer-events: none;
        }
        .service-card:hover::before { opacity: 1; }
        .service-card:hover { transform: translateY(-15px); box-shadow: 0 25px 50px rgba(0,0,0,0.25); background: linear-gradient(135deg, var(--secondary-color), var(--primary-color)); }
        .card-icon { width: 80px; height: 80px; background-color: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; transition: transform 0.5s, background-color 0.5s; flex-shrink: 0; }
        .service-card:hover .card-icon { transform: scale(1.3) translateY(-8px); background-color: rgba(255,255,255,0.3); }
        .card-icon img { width: 40px; height: 40px; filter: invert(1); transition: transform 0.5s; }
        .service-card:hover .card-icon img { transform: scale(1.3); }
        .card-title { font-size: 1.4rem; font-weight: 600; margin-bottom: 15px; transition: transform 0.5s; }
        .service-card:hover .card-title { transform: translateY(-3px); }
        .card-text { font-size: 1rem; opacity: 0.85; margin-bottom: 20px; transition: transform 0.5s; }
        .service-card:hover .card-text { transform: translateY(-3px); }
        .btn-primary-service { border-radius: 50px; padding: 10px 25px; font-weight: 500; transition: transform 0.5s, opacity 0.5s; opacity: 0; transform: translateY(20px); align-self: center; background-color: var(--secondary-color); border: none; }
        .service-card:hover .btn-primary-service { opacity: 1; transform: translateY(0) scale(1.05); background-color: var(--secondary-color); }

        /* ================================
           Service Icon Hover Animation
        ================================ */

        /* Smooth transition */
        .service-card .card-icon {
            transition: transform 0.5s ease, box-shadow 0.5s ease;
        }

        /* Icon image animation */
        .service-card .card-icon img {
            transition: transform 0.5s ease;
        }

        /* Hover effects */
        .service-card:hover .card-icon {
            transform: translateY(-12px) scale(1.15);
            box-shadow: 0 0 0 10px rgba(53, 177, 212, 0.15);
        }

        .service-card:hover .card-icon img {
            transform: rotate(12deg) scale(1.1);
        }

        .service-card .card-icon::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(53, 177, 212, 0.25);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: -1;
        }

        .service-card:hover .card-icon::after {
            opacity: 1;
        }


        /* Review Section */
        .review-section { padding: 80px 0; background-color: var(--bg-light); }
        .review-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding: 20px; background-color: #fff; transition: transform 0.3s; }
        .review-card:hover { transform: translateY(-5px); }

        /* Contact Section */
        /*.contact-section { padding: 80px 0; }*/
        /*.contact-section input, .contact-section textarea { border-radius: 10px; }*/

        /* =========================
   Contact Section
========================= */

        .contact-section {
            background: linear-gradient(
                rgba(2, 93, 97, 0.85),
                rgba(2, 93, 97, 0.85)
            ),
            url("https://images.unsplash.com/photo-1507525428034-b723cf961d3e")
            center/cover no-repeat;
            padding: 100px 0;
            color: #fff;
        }

        .contact-section .contact-card {
            height: 100%;
        }

        /* Equal height fix */
        .contact-section .row > div {
            display: flex;
        }

        /* Glass Cards */
        .contact-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(14px);
            border-radius: 22px;
            padding: 35px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
            height: 100%;
        }

        /* Chairman */
        .chairman-box {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .chairman-box img {
            width: 120px;
            height: 140px;
            border-radius: 12px; /* rectangle, soft corners */
            object-fit: cover;
            border: 3px solid var(--secondary-color);
        }

        .chairman-box h5 {
            margin: 0;
            font-weight: 600;
        }

        .chairman-box span {
            font-size: 0.9rem;
            opacity: 0.85;
        }

        /* Info list */
        .contact-info {
            list-style: none;
            padding: 0;
        }

        .contact-info li {
            margin-bottom: 12px;
        }

        .contact-info a {
            color: #fff;
            opacity: 0.9;
        }

        .contact-info a:hover {
            color: var(--secondary-color);
        }

        /* Contact Form */
        .form-card h4 {
            margin-bottom: 20px;
        }

        .form-card .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            margin-bottom: 15px;
        }

        .form-card .form-control::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .form-card .btn-submit {
            width: 100%;
            background: var(--secondary-color);
            border-radius: 50px;
            padding: 10px;
            border: none;
        }

        .form-card .btn-submit:hover {
            background: #fff;
            color: var(--primary-color);
        }

        /* Map */
        .map-box iframe {
            width: 100%;
            height: 350px;
            border-radius: 22px;
            border: none;
        }


        /* WhatsApp Button */
        .whatsapp-btn { position: fixed; bottom: 20px; right: 20px; background-color: #25D366; color: #fff; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); z-index: 1000; transition: transform 0.3s; }
        .whatsapp-btn:hover { transform: scale(1.1); }

        @media (max-width: 767px) { .hero h1 { font-size: 2rem; } .hero p { font-size: 1rem; } }
    </style>
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
{{--<section id="services" class="service-section">--}}
{{--    <div class="container">--}}
{{--        <div class="row g-4">--}}

{{--            <!-- Travel Planning -->--}}
{{--            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">--}}
{{--                <div class="card service-card h-100">--}}
{{--                    <div class="card-body d-flex flex-column">--}}
{{--                        <div class="card-icon mb-3">--}}
{{--                            <img src="https://img.icons8.com/ios-filled/50/around-the-globe.png" alt="Travel Planning">--}}
{{--                        </div>--}}
{{--                        <h5 class="card-title">Travel Planning</h5>--}}
{{--                        <p class="card-text">Customized travel itineraries for hassle-free and memorable trips.</p>--}}
{{--                        <a href="#" class="btn btn-primary-service mt-auto">Learn More</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <!-- Air Ticket Booking -->--}}
{{--            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="200">--}}
{{--                <div class="card service-card h-100">--}}
{{--                    <div class="card-body d-flex flex-column">--}}
{{--                        <div class="card-icon mb-3">--}}
{{--                            <img src="https://img.icons8.com/ios-filled/50/airplane-take-off.png" alt="Air Ticket Booking">--}}
{{--                        </div>--}}
{{--                        <h5 class="card-title">Air Ticket Booking</h5>--}}
{{--                        <p class="card-text">Book flights quickly and securely at the best rates.</p>--}}
{{--                        <a href="#" class="btn btn-primary-service mt-auto">Learn More</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <!-- Visa Processing -->--}}
{{--            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="300">--}}
{{--                <div class="card service-card h-100">--}}
{{--                    <div class="card-body d-flex flex-column">--}}
{{--                        <div class="card-icon mb-3">--}}
{{--                            <img src="https://img.icons8.com/ios-filled/50/visa.png" alt="Visa Processing">--}}
{{--                        </div>--}}
{{--                        <h5 class="card-title">Visa Processing</h5>--}}
{{--                        <p class="card-text">We handle all visa requirements for hassle-free international travel.</p>--}}
{{--                        <a href="#" class="btn btn-primary-service mt-auto">Learn More</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}

<!-- Reviews Section -->

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

<section id="reviews" class="review-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2>What Our Customers Say</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="review-card p-4">
                    <p>"Amazing service! Travel planning and booking were flawless."</p>
                    <h6>- Sarah K.</h6>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="review-card p-4">
                    <p>"Booking flights was super easy and hassle-free. Highly recommend Upokul Travels."</p>
                    <h6>- James L.</h6>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="review-card p-4">
                    <p>"Visa processing support made our trip stress-free. Excellent experience!"</p>
                    <h6>- Priya M.</h6>
                </div>
            </div>
        </div>
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

<!-- WhatsApp Button -->
<a href="https://wa.me/+8801868-330080" target="_blank" class="whatsapp-btn">
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
</script>
</body>
</html>
