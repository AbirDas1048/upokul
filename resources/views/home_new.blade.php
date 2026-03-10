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

        .btn-google{
            padding:.72rem 1.7rem;
            background:rgba(255,255,255,.04);
            border:1px solid rgba(255,255,255,.12);
            color:var(--muted);
        }
        .btn-google:hover{border-color:#4285F4;color:#4285F4;transform:translateY(-3px)}

        .btn-fb{
            padding:.72rem 1.7rem;
            background:rgba(255,255,255,.04);
            border:1px solid rgba(255,255,255,.12);
            color:var(--muted);
        }
        .btn-fb:hover{border-color:#1877F2;color:#1877F2;transform:translateY(-3px)}

        .btn-block{width:100%;justify-content:center}
        .swal-html-red-bg {
            margin: 10px;
            background-color: #f27474;
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
        }

        /* ══════════════════════════════════════
           NAVBAR
        ══════════════════════════════════════ */
        #mainNav{
            position:fixed;top:0;left:0;right:0;z-index:900;
            padding:1.5rem 0;
            background:transparent;
            transition:padding var(--dur) var(--ease),
            background var(--dur) var(--ease),
            border-color var(--dur);
            border-bottom:1px solid transparent;
        }
        #mainNav.scrolled{
            padding:.9rem 0;
            background:rgba(7,26,26,.93);
            backdrop-filter:blur(22px);
            -webkit-backdrop-filter:blur(22px);
            border-bottom:1px solid var(--border);
            box-shadow:0 8px 40px rgba(0,0,0,.35);
        }

        .nav-inner{
            display:flex;align-items:center;justify-content:space-between;
            gap:1rem;
        }

        .nav-brand{
            display:flex;align-items:center;gap:.6rem;
            font-family:var(--display);font-size:1.6rem;font-weight:600;
            color:var(--white);letter-spacing:.02em;
        }
        .nav-brand-icon{
            width:36px;height:36px;
            border-radius:50%;
            display:flex;align-items:center;justify-content:center;
            font-size:.85rem;flex-shrink:0;
            overflow:hidden;
            border:1px solid rgba(35,205,210,.35);
            background:rgba(7,26,26,.7);
        }
        .nav-brand-icon img{
            width:80%;
            height:80%;
            object-fit:contain;
            filter:brightness(0) invert(1);
            opacity:.95;
            transition:filter var(--dur) var(--ease),opacity var(--dur) var(--ease);
        }
        .nav-brand em{font-style:normal;color:var(--teal-mid)}

        .nav-links{
            display:flex;align-items:center;gap:.3rem;list-style:none;
        }
        .nav-links a{
            font-size:.73rem;font-weight:500;letter-spacing:.13em;text-transform:uppercase;
            color:rgba(255,255,255,.58);
            padding:.46rem .9rem;border-radius:3px;
            position:relative;transition:color .25s;
        }
        .nav-links a::after{
            content:'';position:absolute;bottom:0;left:.9rem;right:.9rem;
            height:1px;background:var(--teal-mid);
            transform:scaleX(0);transition:transform .3s;
        }
        .nav-links a:hover{color:var(--teal-mid)}
        .nav-links a:hover::after{transform:scaleX(1)}

        .nav-right{display:flex;align-items:center;gap:.8rem}

        /* Mobile menu toggle */
        /*.hamburger{*/
        /*    display:none;flex-direction:column;gap:5px;*/
        /*    background:none;border:none;cursor:pointer;padding:4px;*/
        /*}*/
        /*.hamburger span{*/
        /*    width:24px;height:2px;background:var(--white);*/
        /*    border-radius:2px;display:block;transition:var(--dur) var(--ease);*/
        /*}*/
        .hamburger{
            display:none;align-items:center;justify-content:center;
            background:none;border:none;cursor:pointer;
            color:var(--white);
            font-size:1.35rem;
            line-height:1;
            width:34px;height:34px;
            border-radius:50%;
            transition:color var(--dur) var(--ease),background var(--dur) var(--ease),transform var(--dur) var(--ease);
        }
        /*.hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}*/
        /*.hamburger.open span:nth-child(2){opacity:0}*/
        /*.hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}*/
        .hamburger:hover{color:var(--teal-mid);background:rgba(35,205,210,.08)}
        .hamburger.open{color:var(--teal-mid);transform:rotate(90deg)}

        /* Mobile drawer */
        .nav-drawer{
            display:none;position:absolute;top:100%;left:0;right:0;
            background:rgba(7,26,26,.98);
            border-bottom:1px solid var(--border);
            padding:1.5rem;
            flex-direction:column;gap:.2rem;
        }
        .nav-drawer.open{display:flex}
        .nav-drawer a{
            font-size:.78rem;font-weight:500;letter-spacing:.12em;text-transform:uppercase;
            color:rgba(255,255,255,.58);
            padding:.85rem 0;
            border-bottom:1px solid rgba(255,255,255,.04);
            transition:color .25s;
        }
        .nav-drawer a:hover{color:var(--teal-mid)}
        .nav-drawer .btn-primary{margin-top:1rem;justify-content:center}

        @media(max-width:991px){
            .nav-links,.nav-right .btn-primary{display:none}
            .hamburger{display:flex}
            .nav-brand{font-size:1.3rem;max-width:75vw;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
            .nav-brand-text{display:none}
            .nav-drawer{max-height:calc(100vh - 70px);overflow-y:auto}
        }

        @media(max-width:576px){
            .container{padding:0 1rem}
            .nav-brand{gap:.45rem;max-width:calc(100vw - 78px)}
            .nav-brand-icon{width:30px;height:30px}
            .hero-container{padding-top:5.4rem}
            .hero-content h1{font-size:2.2rem;line-height:1.12}
            .hero-content p{font-size:.9rem;line-height:1.65;margin-bottom:1.5rem}
            .hero-btns{gap:.7rem}
            .hero-btns .btn{width:100%;justify-content:center;padding:.74rem 1rem}
            .hero-stat-num{font-size:1.7rem}
            .hero-stat-lbl{font-size:.58rem}
            #about,#services,#gallery,#reviews,#contact{padding:5.5rem 0}
            #trusted{padding:4.5rem 0}
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
           ABOUT
        ══════════════════════════════════════ */
        #about{
            padding:8rem 0;
            background:var(--dark-800);
        }

        .about-grid{
            display:grid;grid-template-columns:1fr 1fr;
            gap:5rem;align-items:center;
        }

        /* Image side */
        .about-img-side{position:relative}

        .about-img-frame{
            position:relative;border-radius:var(--r);overflow:hidden;
        }
        .about-img-frame img{
            width:100%;border-radius:var(--r);
            border:1px solid rgba(35,205,210,.14);
            box-shadow:0 24px 64px rgba(0,0,0,.5);
            transition:transform .6s var(--ease);
        }
        .about-img-frame:hover img{transform:scale(1.02)}

        /* Corner brackets */
        .about-img-frame::before{
            content:'';position:absolute;top:-12px;left:-12px;
            width:64px;height:64px;
            border-top:2px solid var(--teal-mid);
            border-left:2px solid var(--teal-mid);
            border-radius:3px 0 0 0;z-index:1;
        }
        .about-img-frame::after{
            content:'';position:absolute;bottom:-12px;right:-12px;
            width:64px;height:64px;
            border-bottom:2px solid var(--teal-sage);
            border-right:2px solid var(--teal-sage);
            border-radius:0 0 3px 0;z-index:1;
        }

        /* Floating card */
        .about-badge-card{
            position:absolute;bottom:2rem;left:-2rem;
            background:linear-gradient(135deg,var(--dark-700),var(--dark-800));
            border:1px solid rgba(35,205,210,.22);
            border-radius:var(--r);padding:1.1rem 1.5rem;
            display:flex;align-items:center;gap:.9rem;
            box-shadow:0 16px 40px rgba(0,0,0,.5);
            z-index:2;
        }
        .badge-big{
            font-family:var(--display);font-size:2.4rem;font-weight:300;
            color:var(--teal-mid);line-height:1;
        }
        .badge-txt{
            font-size:.72rem;color:var(--muted);line-height:1.4;
        }
        .badge-txt strong{display:block;color:var(--white);font-size:.82rem}

        /* Content side */
        .about-content h2{
            font-family:var(--display);
            font-size:clamp(1.9rem,3.5vw,3rem);
            font-weight:300;line-height:1.15;
            margin-bottom:1.1rem;
        }
        .about-content h2 span{font-style:italic;color:var(--teal-mid)}
        .about-content>p{
            font-size:.9rem;color:var(--muted);line-height:1.85;
            margin-bottom:1.8rem;
        }

        .about-list{list-style:none;margin-bottom:2rem}
        .about-list li{
            display:flex;align-items:flex-start;gap:.75rem;
            font-size:.88rem;color:rgba(255,255,255,.7);
            padding:.65rem 0;
            border-bottom:1px solid rgba(255,255,255,.04);
        }
        .about-list li i{color:var(--teal-mid);font-size:.85rem;margin-top:.15rem;flex-shrink:0}

        @media(max-width:991px){
            .about-grid{grid-template-columns:1fr;gap:3rem}
            .about-badge-card{left:1rem;bottom:1rem}
        }

        /* ══════════════════════════════════════
           SERVICES
        ══════════════════════════════════════ */
        #services{
            padding:8rem 0;
            background:linear-gradient(180deg,var(--dark-900) 0%,var(--dark-800) 100%);
            border-top:1px solid var(--border);
        }

        .services-hd{text-align:center;margin-bottom:4rem}

        .svc-grid{
            display:grid;grid-template-columns:repeat(3,1fr);
            gap:1.4rem;
        }

        .svc-card{
            background:rgba(15,109,111,.07);
            border:1px solid var(--border);
            border-radius:var(--r);
            padding:2.2rem 1.9rem;
            position:relative;overflow:hidden;
            transition:transform var(--dur) var(--ease),
            border-color var(--dur),
            box-shadow var(--dur),
            background var(--dur);
        }
        /* Shimmer top line */
        .svc-card::before{
            content:'';position:absolute;top:0;left:0;right:0;height:2px;
            background:linear-gradient(90deg,var(--teal-deep),var(--teal-mid),var(--teal-sage));
            transform:scaleX(0);transform-origin:left;
            transition:transform .45s var(--ease);
        }
        .svc-card:hover{
            transform:translateY(-9px);
            border-color:rgba(35,205,210,.28);
            box-shadow:0 32px 64px rgba(15,109,111,.22);
            background:rgba(15,109,111,.13);
        }
        .svc-card:hover::before{transform:scaleX(1)}

        .svc-icon{
            width:62px;height:62px;border-radius:50%;
            background:linear-gradient(135deg,rgba(15,109,111,.45),rgba(35,205,210,.15));
            border:1px solid rgba(35,205,210,.22);
            display:flex;align-items:center;justify-content:center;
            margin-bottom:1.5rem;
            transition:var(--dur) var(--ease);
        }
        .svc-card:hover .svc-icon{
            background:linear-gradient(135deg,var(--teal-deep),var(--teal-mid));
            border-color:transparent;
            box-shadow:0 8px 24px rgba(35,205,210,.3);
        }
        .svc-icon i{font-size:1.4rem;color:var(--teal-mid);transition:color .3s}
        .svc-card:hover .svc-icon i{color:#fff}

        .svc-card h5{
            font-family:var(--display);font-size:1.3rem;font-weight:600;
            color:var(--white);margin-bottom:.55rem;
        }
        .svc-card p{font-size:.84rem;color:var(--muted);line-height:1.72}

        @media(max-width:991px){.svc-grid{grid-template-columns:1fr 1fr}}
        @media(max-width:600px) {.svc-grid{grid-template-columns:1fr}}

        /* ══════════════════════════════════════
           COUNTER / TRUST
        ══════════════════════════════════════ */
        #trusted{
            padding:6rem 0;
            background:rgba(15,109,111,.09);
            border-top:1px solid var(--border);
            border-bottom:1px solid var(--border);
            position:relative;overflow:hidden;
        }
        #trusted::before{
            content:'';position:absolute;top:50%;left:50%;
            transform:translate(-50%,-50%);
            width:800px;height:400px;
            background:radial-gradient(ellipse,rgba(35,205,210,.06) 0%,transparent 70%);
            pointer-events:none;
        }

        .trust-hd{text-align:center;margin-bottom:3.5rem}

        .trust-grid{
            display:grid;grid-template-columns:repeat(4,1fr);
            gap:1.4rem;
        }

        .trust-card{
            text-align:center;
            padding:2.5rem 1.5rem;
            border-radius:var(--r);
            background:rgba(255,255,255,.025);
            border:1px solid var(--border);
            transition:var(--dur) var(--ease);
        }
        .trust-card:hover{
            background:rgba(15,109,111,.16);
            border-color:rgba(35,205,210,.26);
            transform:translateY(-5px);
            box-shadow:0 20px 50px rgba(15,109,111,.2);
        }
        .trust-card>i{font-size:2rem;color:var(--teal-mid);margin-bottom:1rem;display:block}
        .trust-card h3{
            font-family:var(--display);font-size:3.8rem;font-weight:300;
            color:var(--teal-mid);line-height:1;margin-bottom:.4rem;
        }
        .trust-card p{
            font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;
            color:rgba(255,255,255,.35);
        }

        @media(max-width:768px){.trust-grid{grid-template-columns:1fr 1fr}}

        /* ══════════════════════════════════════
           GALLERY
        ══════════════════════════════════════ */
        #gallery{
            padding:8rem 0;
            background:var(--dark-800);
        }

        .gallery-hd{text-align:center;margin-bottom:3.5rem}

        /* Swiper custom dots */
        .swiper-pagination-bullet{
            background:rgba(255,255,255,.22)!important;
            opacity:1!important;
            width:8px!important;height:8px!important;
            transition:all .3s!important;
        }
        .swiper-pagination-bullet-active{
            background:var(--teal-mid)!important;
            width:28px!important;
            border-radius:4px!important;
        }

        .gallery-card{
            position:relative;border-radius:var(--r);overflow:hidden;
            cursor:pointer;aspect-ratio:4/3;
        }
        .gallery-card img{
            width:100%;height:100%;object-fit:cover;
            filter:brightness(.76);
            transition:transform .65s var(--ease),filter .45s;
        }
        .gallery-card:hover img{transform:scale(1.08);filter:brightness(.5)}

        .gallery-card-overlay{
            position:absolute;bottom:0;left:0;right:0;
            padding:1.8rem 1.5rem;
            background:linear-gradient(to top,rgba(7,26,26,.96) 0%,transparent 100%);
            transform:translateY(6px);
            transition:transform .35s var(--ease);
        }
        .gallery-card:hover .gallery-card-overlay{transform:translateY(0)}

        .gallery-card-overlay span{
            font-size:.65rem;letter-spacing:.18em;text-transform:uppercase;
            color:var(--teal-mid);
        }
        .gallery-card-overlay h5{
            font-family:var(--display);font-size:1.35rem;font-weight:400;
            color:var(--white);margin:0;
        }

        /* Expand icon */
        .gallery-expand{
            position:absolute;top:50%;left:50%;
            transform:translate(-50%,-50%) scale(.7);
            opacity:0;
            width:52px;height:52px;border-radius:50%;
            background:rgba(35,205,210,.18);
            border:1px solid var(--teal-mid);
            display:flex;align-items:center;justify-content:center;
            color:var(--teal-mid);font-size:1.1rem;
            transition:all .35s var(--ease);
        }
        .gallery-card:hover .gallery-expand{opacity:1;transform:translate(-50%,-50%) scale(1)}

        /* Fullscreen modal */
        .fs-modal{
            position:fixed;inset:0;
            background:rgba(4,14,14,.97);
            z-index:9999;
            display:none;align-items:center;justify-content:center;
        }
        .fs-modal.active{display:flex}
        .fs-swiper{width:90vw;max-width:1100px}
        .fs-swiper img{
            max-height:85vh;width:100%;
            object-fit:contain;border-radius:var(--r);
        }
        .swiper-button-prev,.swiper-button-next{color:var(--teal-mid)!important}

        .fs-close{
            position:absolute;top:1.5rem;right:2rem;
            font-size:2.5rem;line-height:1;
            color:rgba(255,255,255,.45);
            background:none;border:none;cursor:pointer;
            transition:color .2s;z-index:10;
        }
        .fs-close:hover{color:var(--teal-mid)}

        /* ══════════════════════════════════════
           REVIEWS
        ══════════════════════════════════════ */
        #reviews{
            padding:8rem 0;
            background:linear-gradient(180deg,var(--dark-800) 0%,rgba(15,109,111,.07) 100%);
        }
        .reviews-hd{text-align:center;margin-bottom:3.5rem}

        .review-card{
            background:rgba(15,109,111,.07);
            border:1px solid var(--border);
            border-radius:var(--r);padding:2rem;
            height:100%;
            transition:var(--dur) var(--ease);
        }
        .review-card:hover{
            border-color:rgba(35,205,210,.25);
            background:rgba(15,109,111,.13);
            transform:translateY(-5px);
            box-shadow:0 24px 55px rgba(15,109,111,.2);
        }

        .review-stars{color:#f5c542;font-size:.92rem;letter-spacing:.1em;margin-bottom:1rem}

        .review-quote{
            font-family:var(--display);font-style:italic;
            font-size:1.04rem;color:rgba(255,255,255,.74);
            line-height:1.72;margin-bottom:1.5rem;
        }

        .review-author{
            display:flex;justify-content:space-between;align-items:center;
            flex-wrap:wrap;gap:.5rem;
            border-top:1px solid rgba(255,255,255,.05);
            padding-top:1rem;
        }
        .review-author strong{font-size:.85rem;color:var(--white)}
        .review-src{
            display:inline-flex;align-items:center;gap:.4rem;
            font-size:.69rem;color:var(--muted);
        }
        .review-src img{width:14px;height:14px}

        .review-actions{text-align:center;margin-top:3.5rem;display:flex;justify-content:center;gap:1rem;flex-wrap:wrap}

        /* ══════════════════════════════════════
           CONTACT
        ══════════════════════════════════════ */
        #contact{
            padding:8rem 0;
            background:var(--dark-900);
            border-top:1px solid var(--border);
        }
        .contact-hd{text-align:center;margin-bottom:3.5rem}

        .contact-grid{
            display:grid;grid-template-columns:1fr 1fr;
            gap:2rem;
        }

        .contact-card{
            background:rgba(15,109,111,.07);
            border:1px solid var(--border);
            border-radius:var(--r);padding:2.6rem;
        }
        .contact-card h4{
            font-family:var(--display);font-size:1.6rem;font-weight:400;
            color:var(--white);margin-bottom:1.8rem;
        }

        .form-group{margin-bottom:1.1rem}
        .form-group input,
        .form-group textarea{
            width:100%;
            padding:.88rem 1.1rem;
            background:rgba(255,255,255,.04);
            border:1px solid rgba(255,255,255,.09);
            border-radius:4px;
            color:var(--white);
            font-family:var(--body);font-size:.86rem;
            outline:none;
            transition:border-color .3s,background .3s;
            resize:none;
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder{color:rgba(255,255,255,.26)}
        .form-group input:focus,
        .form-group textarea:focus{
            border-color:var(--teal-mid);
            background:rgba(35,205,210,.05);
        }

        /* Map */
        .map-box{
            border-radius:var(--r);overflow:hidden;
            margin-bottom:1.8rem;border:1px solid var(--border);
        }
        .map-box iframe{width:100%;height:220px;border:none;display:block}

        .company-info h4{
            font-family:var(--display);font-size:1.4rem;font-weight:400;
            color:var(--white);margin-bottom:.9rem;
        }
        .company-info p{font-size:.84rem;color:var(--muted);line-height:2.2}
        .company-info i{color:var(--teal-mid);margin-right:.4rem;width:16px}

        .owner-box{
            display:flex;align-items:flex-start;gap:1rem;
            margin-top:1.6rem;padding-top:1.6rem;
            border-top:1px solid rgba(255,255,255,.05);
        }
        .owner-avatar{
            width:50px;height:50px;border-radius:50%;flex-shrink:0;
            background:linear-gradient(135deg,var(--teal-deep),var(--teal-sage));
            border:2px solid var(--teal-mid);
            display:flex;align-items:center;justify-content:center;
            font-family:var(--display);font-size:1.3rem;color:#fff;
        }
        .owner-meta{display:flex;flex-direction:column;gap:.28rem}
        .owner-meta strong{font-size:.85rem;color:var(--white)}
        .owner-meta span{font-size:.78rem;color:var(--muted)}
        .owner-meta i{color:var(--teal-mid);margin-right:.35rem}

        @media(max-width:768px){.contact-grid{grid-template-columns:1fr}}

        /* ══════════════════════════════════════
           FOOTER
        ══════════════════════════════════════ */
        footer{
            padding:3rem 0;
            background:rgba(0,0,0,.55);
            border-top:1px solid rgba(35,205,210,.07);
            text-align:center;
        }
        .footer-links{
            display:flex;justify-content:center;
            gap:2.2rem;flex-wrap:wrap;margin-bottom:1.4rem;
        }
        .footer-links a{
            font-size:.7rem;letter-spacing:.17em;text-transform:uppercase;
            color:rgba(255,255,255,.28);transition:color .25s;
        }
        .footer-links a:hover{color:var(--teal-mid)}
        .footer-copy{font-size:.72rem;color:rgba(255,255,255,.18)}

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

        /* ─── FLOATING WHATSAPP BUTTON ───────────────────────────── */
        .whatsapp-float {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 0;
            cursor: pointer;
            text-decoration: none;
        }

        /* The icon circle */
        .whatsapp-float .wa-icon {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, #25D366, #128C7E);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: #fff;
            box-shadow: 0 6px 24px rgba(37,211,102,0.50);
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            flex-shrink: 0;
            z-index: 1;
        }

        /* Pulse ring animation */
        .whatsapp-float .wa-icon::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2.5px solid rgba(37,211,102,0.55);
            animation: wa-pulse 2s ease-out infinite;
        }

        .whatsapp-float .wa-icon::after {
            content: '';
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            border: 2px solid rgba(37,211,102,0.25);
            animation: wa-pulse 2s ease-out infinite 0.4s;
        }

        @keyframes wa-pulse {
            0%   { transform: scale(1);   opacity: 1; }
            100% { transform: scale(1.55); opacity: 0; }
        }

        /* Tooltip label */
        .whatsapp-float .wa-label {
            background: #fff;
            color: #128C7E;
            font-family: var(--font-body);
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
            padding: 0.45rem 1rem 0.45rem 1.2rem;
            border-radius: 999px 0 0 999px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            transform: translateX(16px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid rgba(37,211,102,0.2);
            border-right: none;
        }

        /* Hover effects */
        .whatsapp-float:hover .wa-icon {
            transform: scale(1.10);
            box-shadow: 0 10px 32px rgba(37,211,102,0.60);
        }

        .whatsapp-float:hover .wa-label {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        @media (max-width: 480px) {
            .whatsapp-float {
                bottom: 18px;
                right: 18px;
            }
            .whatsapp-float .wa-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
            /* Hide label on small screens to avoid overflow */
            .whatsapp-float .wa-label { display: none; }
        }


        /* Ultra-small devices */
        @media (max-width:349px){
            .container{padding:0 .85rem}
            .nav-inner{gap:.45rem;min-width:0}
            .nav-brand{font-size:1.02rem;gap:.35rem;max-width:calc(100% - 40px);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
            .nav-brand-icon{width:26px;height:26px;font-size:.65rem}
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
            .contact-card{padding:1.1rem}
            .review-actions .btn{width:100%;justify-content:center}
            .footer-links{gap:.75rem}
        }

    </style>
</head>
<body>

<!-- ░░░ NAVBAR ░░░ -->
<nav id="mainNav">
    <div class="container">
        <div class="nav-inner">

            <a href="#hero" class="nav-brand">
                <div class="nav-brand-icon">
                    <img src="{{ asset('images/Logo.png') }}" alt="{{ config('app.name') }}">
                </div>
                <span class="nav-brand-text">{{ config('app.name') }}<em>.</em></span>
            </a>

            <ul class="nav-links">
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#gallery">Gallery</a></li>
                <li><a href="#reviews">Reviews</a></li>
            </ul>

            <div class="nav-right">
                <a href="#contact" class="btn btn-primary">Contact Us</a>
                <button class="hamburger" id="ham" aria-label="Toggle menu" aria-expanded="false" aria-controls="drawer">
                    <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>
                </button>
            </div>

        </div>

        <!-- Mobile drawer -->
        <div class="nav-drawer" id="drawer">
            <a href="#about" class="drawer-link">About</a>
            <a href="#services" class="drawer-link">Services</a>
            <a href="#gallery" class="drawer-link">Gallery</a>
            <a href="#reviews" class="drawer-link">Reviews</a>
            <a href="#contact" class="btn btn-primary drawer-link">Contact Us</a>
        </div>
    </div>
</nav>

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

<!-- ░░░ ABOUT ░░░ -->
@include('partials.sections.about', ['variant' => 'modern'])
<!-- ░░░ SERVICES ░░░ -->
@include('partials.sections.services', ['variant' => 'modern'])

<!-- ░░░ COUNTER ░░░ -->
<section id="trusted">
    <div class="container">
        <div class="trust-hd">
            <h2 class="section-title" data-reveal>Trusted by Travelers Worldwide</h2>
            <p class="section-sub" data-reveal style="transition-delay:.05s">
                Numbers that reflect our experience, trust, and global reach
            </p>
        </div>
        <div class="trust-grid">
            <div class="trust-card" data-reveal>
                <i class="fas fa-smile-beam"></i>
                <h3 class="counter" data-count="100">0</h3>
                <p>Satisfied Clients</p>
            </div>
            <div class="trust-card" data-reveal style="transition-delay:.05s">
                <i class="fas fa-globe-asia"></i>
                <h3 class="counter" data-count="300">0</h3>
                <p>Countries Connected</p>
            </div>
            <div class="trust-card" data-reveal style="transition-delay:.05s">
                <i class="fas fa-hotel"></i>
                <h3 class="counter" data-count="800">0</h3>
                <p>Hotel Partnerships</p>
            </div>
            <div class="trust-card" data-reveal style="transition-delay:.02s">
                <i class="fas fa-handshake"></i>
                <h3 class="counter" data-count="500">0</h3>
                <p>Trusted Agents & Media</p>
            </div>
        </div>
    </div>
</section>

<!-- ░░░ GALLERY ░░░ -->
<section id="gallery">
    <div class="container">
        <div class="gallery-hd">
            <span class="section-label" data-reveal>Travel Moments</span>
            <h2 class="section-title" data-reveal style="transition-delay:.05s">Explore Our <span>Destinations</span></h2>
            <p class="section-sub" data-reveal style="transition-delay:.05s">Unforgettable journeys captured in every frame</p>
        </div>

        <div class="swiper cardSwiper" style="padding-bottom:3rem">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="gallery-card" data-index="0">
                        <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=65&auto=format" alt="Cox's Bazar">
                        <div class="gallery-expand"><i class="fas fa-expand"></i></div>
                        <div class="gallery-card-overlay"><span>Bangladesh</span><h5>Cox's Bazar</h5></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gallery-card" data-index="1">
                        <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=600&q=65&auto=format" alt="Sylhet">
                        <div class="gallery-expand"><i class="fas fa-expand"></i></div>
                        <div class="gallery-card-overlay"><span>Bangladesh</span><h5>Sylhet</h5></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gallery-card" data-index="2">
                        <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=600&q=65&auto=format" alt="Bali">
                        <div class="gallery-expand"><i class="fas fa-expand"></i></div>
                        <div class="gallery-card-overlay"><span>Indonesia</span><h5>Bali</h5></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gallery-card" data-index="3">
                        <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600&q=65&auto=format" alt="Sundarbans">
                        <div class="gallery-expand"><i class="fas fa-expand"></i></div>
                        <div class="gallery-card-overlay"><span>Bangladesh</span><h5>Sundarbans</h5></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gallery-card" data-index="4">
                        <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=600&q=65&auto=format" alt="Maldives">
                        <div class="gallery-expand"><i class="fas fa-expand"></i></div>
                        <div class="gallery-card-overlay"><span>Maldives</span><h5>Island Paradise</h5></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gallery-card" data-index="5">
                        <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=65&auto=format" alt="Bali Temple">
                        <div class="gallery-expand"><i class="fas fa-expand"></i></div>
                        <div class="gallery-card-overlay"><span>Indonesia</span><h5>Bali Temples</h5></div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- Fullscreen Modal -->
<div class="fs-modal" id="fsModal">
    <div class="swiper fs-swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=70&auto=format" alt=""></div>
            <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=1200&q=70&auto=format" alt=""></div>
            <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=1200&q=70&auto=format" alt=""></div>
            <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=1200&q=70&auto=format" alt=""></div>
            <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=1200&q=70&auto=format" alt=""></div>
            <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1200&q=70&auto=format" alt=""></div>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
    <button class="fs-close" id="fsClose">&times;</button>
</div>

<!-- ░░░ REVIEWS ░░░ -->
<section id="reviews">
    <div class="container">
        <div class="reviews-hd">
            <span class="section-label" data-reveal>Testimonials</span>
            <h2 class="section-title" data-reveal style="transition-delay:.05s">What Travelers <span>Say</span></h2>
            <p class="section-sub" data-reveal style="transition-delay:.05s">Real experiences from our Google Business & Facebook profiles</p>
        </div>

        <div class="swiper reviewSwiper" style="padding-bottom:3.5rem">
            <div class="swiper-wrapper">

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-quote">"Amazing service! Visa processing and hotel booking were handled so smoothly. Highly recommended to everyone."</p>
                        <div class="review-author">
                            <strong>Rahim Ahmed</strong>
                            <span class="review-src">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="G">
                Google Review
              </span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-quote">"Very professional team. Our family tour was perfectly planned and completely stress-free. Will book again!"</p>
                        <div class="review-author">
                            <strong>Nusrat Jahan</strong>
                            <span class="review-src">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="G">
                Google Review
              </span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-quote">"Best travel agency in Bangladesh. Quick support, transparent pricing, and they genuinely care about travelers."</p>
                        <div class="review-author">
                            <strong>Tanvir Hasan</strong>
                            <span class="review-src">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="G">
                Google Review
              </span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-quote">"They handled everything from flights to hotels perfectly. The whole trip was magical — absolutely loved it!"</p>
                        <div class="review-author">
                            <strong>Farida Begum</strong>
                            <span class="review-src">
                <img src="https://upload.wikimedia.org/wikipedia/commons/1/1b/Facebook_icon.svg" alt="F">
                Facebook Review
              </span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-quote">"Exceptional experience. The team was attentive and made our honeymoon in Maldives absolutely unforgettable."</p>
                        <div class="review-author">
                            <strong>Karim & Sadia</strong>
                            <span class="review-src">
                <img src="https://upload.wikimedia.org/wikipedia/commons/1/1b/Facebook_icon.svg" alt="F">
                Facebook Review
              </span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="swiper-pagination"></div>
        </div>

        <div class="review-actions">
            <a href="https://search.google.com/local/writereview?placeid=ChIJb5Q_kd_ZrDARzdblKHT4xYo" target="_blank" rel="noopener noreferrer" class="btn btn-google">
                <i class="fab fa-google"></i> Google Reviews
            </a>
            <a href="https://www.facebook.com/profile.php?id=61555938637948&sk=reviews" target="_blank" rel="noopener noreferrer" class="btn btn-fb">
                <i class="fab fa-facebook-f"></i> Facebook Reviews
            </a>
        </div>
    </div>
</section>

<!-- ░░░ CONTACT ░░░ -->
<section id="contact">
    <div class="container">
        <div class="contact-hd">
            <span class="section-label" data-reveal>Get In Touch</span>
            <h2 class="section-title" data-reveal style="transition-delay:.05s">Contact <span>Us</span></h2>
            <p class="section-sub" data-reveal style="transition-delay:.05s">We'd love to hear from you. Get in touch anytime.</p>
        </div>

        <div class="contact-grid">

            <!-- Form -->
            <div class="contact-card" data-reveal>
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
                    <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <!-- Map + Info -->
            <div class="contact-card" data-reveal style="transition-delay:.02s">
                <div class="map-box">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3692.600173850267!2d91.81103287529328!3d22.325913979668194!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30acd9df913f946f%3A0x8ac5f87428e5d6cd!2sUpokul%20Travels%20%26%20Holidays!5e0!3m2!1sen!2sbd!4v1768747684228!5m2!1sen!2sbd"
                        loading="lazy" allowfullscreen>
                    </iframe>
                </div>

                <div class="company-info">
                    <h4>{{config('app.name')}}</h4>
                    <p>
                        <i class="fas fa-location-dot"></i> {{ config('custom.office_address') }}<br>
                        <i class="fas fa-envelope"></i> {{ config('custom.office_email') }}<br>
                        <i class="fas fa-phone"></i> {{ config('custom.office_phone') }}
                    </p>
                </div>

                <div class="owner-box">
                    <div class="owner-avatar">M</div>
                    <div class="owner-meta">
                        <strong><i class="fas fa-user-tie"></i> {{ config('custom.owner_designation') }}: Mr. {{ config('custom.owner_name') }}</strong>
                        <span><i class="fas fa-envelope"></i> {{ config('custom.owner_email') }}</span>
                        <span><i class="fas fa-phone"></i> {{ config('custom.owner_phone') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ░░░ FOOTER ░░░ -->
<footer>
    <div class="container">
        <div class="footer-links">
            <a href="#about">About</a>
            <a href="#services">Services</a>
            <a href="#gallery">Gallery</a>
            <a href="#reviews">Reviews</a>
            <a href="#contact">Contact</a>
        </div>
        <p class="footer-copy">
            © {{ date('Y') }} {{ config('app.name') }}.
            All Rights Reserved.
        </p>
    </div>
</footer>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/{{config('custom.whatsapp_number')}}" target="_blank" rel="noopener noreferrer" class="whatsapp-float" aria-label="Chat on WhatsApp">
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
