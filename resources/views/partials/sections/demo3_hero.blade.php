<section id="hero" class="hero-section" aria-labelledby="demo3-hero-title">
    <video class="hero-video" autoplay muted loop playsinline tabindex="-1" preload="metadata" poster="{{ asset('images/banner.jpg') }}">
        <source src="{{ asset('videos/bg.mp4') }}" type="video/mp4">
        <track kind="captions" srclang="en" src="{{ asset('videos/captions.vtt') }}" label="English">
    </video>

    <div class="hero-overlay"></div>

    <div class="container">
        <div class="row">
            <div class="col-lg-6 hero-content">
                <h1 id="demo3-hero-title">
                    Discover Beautiful Places <br>
                    <span>With {{ config('app.name') }}</span>
                </h1>

                <p>
                    We create unforgettable travel experiences with comfort,
                    safety and trusted service across Bangladesh &amp; beyond.
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
