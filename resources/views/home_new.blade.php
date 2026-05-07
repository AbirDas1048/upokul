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
    <style>

        /* ══════════════════════════════════════
           ROOT TOKENS
        ══════════════════════════════════════ */
        :root {
            --teal-deep : #0f6d6f;
            --teal-mid  : #23cdd2;
            --teal-sage : #6c9b98;
            --dark-900  : #040e0e;
            --dark-800  : #071a1a;
            --dark-700  : #0c2424;
            --gold      : #c9a96e;
            --white     : #ffffff;
            --muted     : rgba(255,255,255,.48);
            --border    : rgba(35,205,210,.1);

            --display : 'Cormorant Garamond', serif;
            --body    : 'Poppins', sans-serif;

            --ease    : cubic-bezier(.4,0,.2,1);
            --dur     : .38s;
            --r       : 8px;

            --nav-bg: transparent;
            --nav-bg-scrolled: rgba(7,26,26,.93);

            --nav-text:#fff;
            --nav-link:rgba(255,255,255,.6);

            --nav-accent:#23cdd2;
            --nav-accent-soft:rgba(35,205,210,.35);

            --nav-border:rgba(35,205,210,.2);
            --nav-shadow:0 8px 40px rgba(0,0,0,.35);

            --btn-bg:#23cdd2;
            --btn-text:#003b3d;
            --btn-shadow:0 6px 20px rgba(35,205,210,.35);

            --nav-icon-bg:rgba(7,26,26,.7);

            --about-bg:var(--dark-800);

            --about-heading:#fff;
            --about-text:rgba(255,255,255,.75);
            --about-muted:rgba(255,255,255,.6);
            --about-accent:#23cdd2;

            --about-divider:rgba(255,255,255,.05);

            --about-img-shadow:0 24px 64px rgba(0,0,0,.6);

            --about-glow:none;

            --about-watermark:'';
            --about-watermark-color:transparent;

            --about-float-icon:'✦';
            --about-float-color:rgba(255,255,255,.75);

            --services-bg: linear-gradient(180deg,#0f2027,#203a43);

            --badge-bg: rgba(35,205,210,0.15);
            --badge-color: #23cdd2;

            --title-color: #ffffff;
            --title-gradient: linear-gradient(90deg,#23cdd2,#9be7c4);

            --service-text-muted: #9aa4af;

            --card-bg: rgba(15,109,111,0.08);
            --card-border: rgba(35,205,210,0.15);
            --card-border-hover: rgba(35,205,210,0.35);

            --card-shadow: 0 10px 30px rgba(0,0,0,0.2);
            --card-shadow-hover: 0 32px 64px rgba(15,109,111,0.25);

            --card-gradient: linear-gradient(90deg,#0f6d6f,#23cdd2,#9be7c4);

            --icon-bg: rgba(15,109,111,0.3);
            --icon-bg-hover: linear-gradient(135deg,#0f6d6f,#23cdd2);

            --icon-color: #23cdd2;
            --icon-color-hover: #fff;

            --icon-shadow-hover: 0 8px 24px rgba(35,205,210,0.3);

            --trust-bg: rgba(15,109,111,.09);

            --trust-title: var(--teal-deep);
            --trust-sub: rgba(15,109,111,.7);

            --trust-card-bg: rgba(255,255,255,.05);
            --trust-card-border: rgba(15,109,111,.2);
            --trust-card-shadow: none;
            --trust-card-hover-shadow: 0 20px 50px rgba(15,109,111,.2);

            --trust-icon: var(--teal-mid);
            --trust-number: var(--teal-mid);
            --trust-plus: var(--teal-mid);
            --trust-text: rgba(15,109,111,.6);

            --gallery-bg: #071a1a;

            --gallery-shadow: none;
            --gallery-shadow-hover: none;

            --gallery-overlay: linear-gradient(to top, rgba(7,26,26,0.96), transparent);

            --gallery-title-color: #fff;
            --gallery-subtitle-color: var(--teal-mid);

            --gallery-expand-bg: rgba(35,205,210,0.18);
            --gallery-expand-border: var(--teal-mid);
            --gallery-expand-color: var(--teal-mid);

            --gallery-img-filter-hover: brightness(0.5);

            --pagination-color: rgba(255,255,255,0.3);
            --pagination-active: var(--teal-mid);

            --modal-bg: rgba(4,14,14,0.97);

            --close-bg: transparent;
            --close-color: rgba(255,255,255,0.6);

            --reviews-bg: linear-gradient(180deg,var(--dark-800) 0%,rgba(15,109,111,.07) 100%);;

            --review-card-bg: rgba(15,109,111,.07);
            --review-card-hover-bg: rgba(15,109,111,.13);

            --review-border: rgba(255,255,255,.08);
            --review-border-hover: rgba(35,205,210,.25);

            --review-shadow-hover: 0 24px 55px rgba(15,109,111,.2);

            --review-text: rgba(255,255,255,.75);
            --review-author: #fff;
            --review-muted: #94a3b8;

            --review-divider: rgba(255,255,255,.08);
            --review-star: #f5c542;

            --review-quote-bg: rgba(35,205,210,.12);

            --review-btn-bg: rgba(35,205,210,0.08);
            --review-btn-border: rgba(35,205,210,0.25);
            --review-btn-text: #ffffff;

            --review-btn-hover-bg: var(--teal-mid);
            --review-btn-hover-border: var(--teal-mid);
            --review-btn-hover-text: #ffffff;

            --review-btn-shadow: 0 14px 36px rgba(35,205,210,.35);

            --contact-bg:var(--dark-900);
            --contact-card-bg:rgba(15,109,111,.07);
            --contact-border:var(--border);

            --contact-text:#ffffff;
            --contact-muted:rgba(255,255,255,.6);

            --contact-input-bg:rgba(255,255,255,.04);
            --contact-input-border:rgba(255,255,255,.1);

            --contact-accent:var(--teal-mid);

            /* BUTTON */
            --contact-btn-bg: var(--teal-mid);
            --contact-btn-border: var(--teal-mid);
            --contact-btn-text: #ffffff;

            --footer-bg: rgba(0,0,0,.55);
            --footer-border: rgba(35,205,210,.07);
            --footer-link: rgba(255,255,255,.28);
            --footer-link-hover: var(--teal-mid);
            --footer-copy: rgba(255,255,255,.18);
        }

        /* ══════════════════════════════════════
           RESET & BASE
        ══════════════════════════════════════ */
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:auto;font-size:16px;overflow-x:hidden;width:100%}
        body{
            width:100%;
            max-width:100vw;
            font-family:var(--body);
            background:var(--dark-900);
            color:var(--white);
            overflow-x:hidden;
            line-height:1.6;
        }
        img{display:block;max-width:100%}
        a{text-decoration:none}

        /* Prevent text overflow on ultra-small screens */
        p,h1,h2,h3,h4,h5,h6,a,span,strong{overflow-wrap:anywhere;word-break:break-word}

        /* Respect reduced-motion and avoid costly animations for sensitive users/devices */
        @media (prefers-reduced-motion: reduce){
            *,*::before,*::after{animation:none !important;transition:none !important;scroll-behavior:auto !important}
            .hero-bg{transform:none !important}
        }

        /* ══════════════════════════════════════
           UTILITY
        ══════════════════════════════════════ */
        .container{
            width:100%;
            max-width:1180px;
            margin:0 auto;
            padding:0 1.5rem;
        }

        .section-label{
            display:inline-flex;align-items:center;gap:.5rem;
            padding:.32rem 1.1rem;
            border:1px solid rgba(35,205,210,.3);
            border-radius:20px;
            font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;
            color:var(--teal-mid);
            background:rgba(35,205,210,.06);
            margin-bottom:1rem;
        }
        .section-label::before{content:'✦';font-size:.55rem}

        .section-title{
            font-family:var(--display);
            font-size:clamp(2rem,4vw,3.2rem);
            font-weight:300;line-height:1.1;
            color:var(--white);
        }
        .section-title em,.section-title span{font-style:italic;color:var(--teal-mid)}

        .section-sub{
            font-size:.9rem;color:var(--muted);
            max-width:520px;margin:.9rem auto 0;
            line-height:1.8;
        }

        /* ══════════════════════════════════════
           BUTTONS
        ══════════════════════════════════════ */
        .btn{
            display:inline-flex;align-items:center;gap:.5rem;
            font-family:var(--body);font-size:.77rem;
            font-weight:600;letter-spacing:.13em;text-transform:uppercase;
            border-radius:4px;border:none;cursor:pointer;
            transition:transform var(--dur) var(--ease),
            box-shadow var(--dur) var(--ease),
            background var(--dur);
            white-space:nowrap;
        }
        .btn-primary{
            padding:.82rem 2rem;
            background:linear-gradient(135deg,var(--teal-deep),var(--teal-mid));
            color:#fff;
        }
        .btn-primary:hover{transform:translateY(-3px);box-shadow:0 14px 36px rgba(35,205,210,.38);color:#fff}

        .btn-outline{
            padding:.82rem 2rem;
            background:rgba(255,255,255,.08);
            border:1.5px solid rgba(255,255,255,.7);
            color:#fff;
        }
        .btn-outline:hover{border-color:var(--teal-mid);color:var(--teal-mid);background:rgba(35,205,210,.08);transform:translateY(-3px)}

        .btn-block{width:100%;justify-content:center}
        .swal-html-red-bg {
            margin: 10px;
            background-color: #f27474;
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
        }

        @media(max-width:576px){
            .container{padding:0 1rem}
            .hero-container{padding-top:5.4rem}
            .hero-content h1{font-size:2.2rem;line-height:1.12}
            .hero-content p{font-size:.9rem;line-height:1.65;margin-bottom:1.5rem}
            .hero-btns{gap:.7rem}
            .hero-btns .btn{width:100%;justify-content:center;padding:.74rem 1rem}
            .hero-stat-num{font-size:1.7rem}
            .hero-stat-lbl{font-size:.58rem}
            #about{padding:5.5rem 0}
        }

        /* ══════════════════════════════════════
           HERO
        ══════════════════════════════════════ */
        #hero{
            position:relative;min-height:100vh;
            display:flex;align-items:center;
            overflow:hidden;
        }

        .hero-bg{
            position:absolute;inset:0;
            background:url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=1200&q=70&auto=format')
            center/cover no-repeat;
            transform:scale(1.0);
            animation:kenBurns 18s ease-in-out infinite alternate;
            z-index:0;
            will-change:transform;
        }
        @keyframes kenBurns{
            from{transform:scale(1.0)}
            to  {transform:scale(1.05)}
        }

        .hero-overlay{
            position:absolute;inset:0;z-index:1;
            /* Base dark layer — always opaque enough */
            background:rgba(4,14,14,.78);
        }
        /* Second overlay for teal tint — layered on top */
        .hero-overlay::after{
            content:'';position:absolute;inset:0;
            background:linear-gradient(
                135deg,
                rgba(4,14,14,.55)   0%,
                rgba(15,109,111,.25) 55%,
                rgba(4,14,14,.45)   100%
            );
        }

        .hero-noise{
            position:absolute;inset:0;z-index:2;opacity:.03;
            background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            background-size:256px;
        }

        .hero-grid{
            position:absolute;inset:0;z-index:2;
            background-image:
                linear-gradient(rgba(35,205,210,.035) 1px,transparent 1px),
                linear-gradient(90deg,rgba(35,205,210,.035) 1px,transparent 1px);
            background-size:68px 68px;
        }

        /* Glow orbs */
        .hero-orb{
            position:absolute;border-radius:50%;
            filter:blur(90px);pointer-events:none;z-index:2;
        }
        .orb-a{
            width:520px;height:520px;
            right:-80px;top:50%;transform:translateY(-50%);
            background:rgba(15,109,111,.28);
            animation:orbFloat 9s ease-in-out infinite;
        }
        .orb-b{
            width:260px;height:260px;
            left:8%;bottom:12%;
            background:rgba(35,205,210,.12);
            animation:orbFloat 12s ease-in-out infinite reverse;
        }
        @keyframes orbFloat{
            0%,100%{transform:translateY(-50%)}
            50%{transform:translateY(calc(-50% - 30px))}
        }
        .orb-b{transform:none!important;animation:orbFloatB 12s ease-in-out infinite reverse}
        @keyframes orbFloatB{0%,100%{transform:translateY(0)}50%{transform:translateY(-25px)}}

        .hero-container{position:relative;z-index:10;width:100%;padding-top:6rem;padding-bottom:13rem}

        .hero-eyebrow{
            display:inline-flex;align-items:center;gap:.5rem;
            padding:.38rem 1.2rem;
            border:1px solid rgba(35,205,210,.6);border-radius:20px;
            font-size:.67rem;letter-spacing:.22em;text-transform:uppercase;
            color:var(--teal-mid);
            background:rgba(4,14,14,.6);
            backdrop-filter:blur(8px);
            margin-bottom:1.6rem;
            animation:fadeUp .9s ease .1s both;
        }
        .hero-eyebrow::before{content:'✦';font-size:.55rem}

        .hero-content{max-width:640px}

        .hero-content h1{
            font-family:var(--display);
            font-size:clamp(3rem,7.5vw,6rem);
            font-weight:300;line-height:1.05;
            margin-bottom:1.4rem;
            color:#fff;
            text-shadow:0 2px 20px rgba(0,0,0,.7);
            animation:fadeUp .9s ease .2s both;
        }
        .hero-content h1 em{
            display:block;font-style:italic;
            color:var(--teal-mid);
        }

        .hero-content p{
            font-size:1rem;
            color:rgba(255,255,255,.88);
            line-height:1.82;max-width:500px;
            margin-bottom:2.5rem;
            text-shadow:0 1px 8px rgba(0,0,0,.6);
            animation:fadeUp .9s ease .3s both;
        }

        .hero-btns{
            display:flex;gap:1rem;flex-wrap:wrap;
            animation:fadeUp .9s ease .4s both;
        }

        /* Stats strip inside hero */
        .hero-stats{
            position:absolute;bottom:0;left:0;right:0;z-index:10;
            background:rgba(4,14,14,.94);
            backdrop-filter:none;
            -webkit-backdrop-filter:none;
            border-top:1px solid var(--border);
        }
        .hero-stats-inner{
            display:grid;grid-template-columns:repeat(4,1fr);
        }
        .hero-stat{
            padding:1.4rem 2rem;text-align:center;
            border-right:1px solid var(--border);
        }
        .hero-stat:last-child{border-right:none}
        .hero-stat-num{
            font-family:var(--display);font-size:2.1rem;font-weight:300;
            color:var(--teal-mid);line-height:1;
        }
        .hero-stat-lbl{
            font-size:.65rem;letter-spacing:.16em;text-transform:uppercase;
            color:var(--muted);margin-top:.25rem;
        }

        @keyframes fadeUp{
            from{opacity:0;transform:translateY(28px)}
            to  {opacity:1;transform:translateY(0)}
        }

        @media(max-width:768px){
            .hero-content h1{font-size:2.8rem}
            .hero-btns{flex-direction:column}
            .hero-btns .btn{justify-content:center}
            .hero-stats-inner{grid-template-columns:1fr 1fr}
            .hero-stat{padding:1rem}
            .hero-stat:nth-child(2){border-right:none}
            .hero-container{padding-bottom:18rem} /* stats are 2 rows tall on mobile */
        }

        @media(max-width:768px){
            .hero-noise,.hero-grid,.hero-orb{display:none}
        }

        /* ══════════════════════════════════════
           SCROLL REVEAL
        ══════════════════════════════════════ */
        [data-reveal]{
            opacity:0;transform:translateY(16px);
            transition:opacity .45s var(--ease),transform .45s var(--ease);
        }
        [data-reveal].visible{opacity:1;transform:none}

        /* During window resize — kill costly animations/effects instantly */
        body.is-resizing *,
        body.is-resizing *::before,
        body.is-resizing *::after{
            animation:none !important;
            transition:none !important;
        }
        body.is-resizing [data-reveal],
        body.is-resizing [data-reveal].visible{
            opacity:1 !important;
            transform:none !important;
        }
        body.is-resizing .hero-noise,
        body.is-resizing .hero-grid,
        body.is-resizing .hero-orb{
            display:none !important;
        }


        /* Ultra-small devices */
        @media (max-width:349px){
            .container{padding:0 .85rem}
            .btn{white-space:normal}

            /* Keep hero content visible on ultra-small screens */
            #hero{min-height:auto;display:block;overflow:hidden}
            .hero-container{padding-top:4.9rem;padding-bottom:0}
            .hero-content{max-width:100%;min-width:0}
            .hero-content h1{font-size:1.76rem;line-height:1.14}
            .hero-content p{font-size:.82rem;line-height:1.58;max-width:100%;margin-bottom:1rem}
            .hero-btns{gap:.55rem}
            .hero-btns .btn{width:100%;justify-content:center;padding:.72rem .8rem;white-space:normal;line-height:1.35}

            /* Remove overlay collision from sticky stats */
            .hero-stats{
                position:relative !important;
                left:auto;right:auto;bottom:auto;
                display:block !important;
                visibility:visible !important;
                opacity:1 !important;
                width:100%;
                margin-top:.35rem;
                z-index:20;
            }
            .hero-stats-inner{grid-template-columns:1fr}
            .hero-stat{padding:.82rem .65rem;border-right:none;border-bottom:1px solid var(--border)}
            .hero-stat:last-child{border-bottom:none}

            /* Trusted counter section fit */
            #trusted{padding:3.2rem 0}
            .trust-grid{grid-template-columns:1fr;gap:.9rem}
            .trust-card{padding:1.3rem .85rem}
            .trust-card h3{font-size:2.15rem}
            .trust-card p{font-size:.58rem;letter-spacing:.1em}

            .section-title{font-size:1.48rem;line-height:1.2}
            .section-sub{font-size:.78rem}
            .about-badge-card{left:.4rem;right:.4rem;padding:.72rem .9rem}
            .badge-big{font-size:1.7rem}
        }

    </style>
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
<script src="{{ asset('js/common-interactions.js') }}"></script>
</body>
</html>
