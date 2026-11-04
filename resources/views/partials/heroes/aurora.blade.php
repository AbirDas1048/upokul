<section id="hero" class="hero hero-aurora" style="background-image:url('{{ asset('images/banner.jpg') }}')" aria-labelledby="demo1-hero-title">
    <video class="hero-video" autoplay muted loop playsinline tabindex="-1" preload="metadata" poster="{{ asset('images/banner.jpg') }}" aria-hidden="true">
        <source src="{{ asset('videos/bg.mp4') }}" type="video/mp4">
        <track kind="captions" srclang="en" src="{{ asset('videos/captions.vtt') }}" label="English">
    </video>
    <div class="hero-overlay"></div>

    <div class="container hero-inner hero-anim">
        <span class="hero-eyebrow"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Travels &amp; Holidays · Chattogram, Bangladesh</span>
        <h1 id="demo1-hero-title">Discover Beautiful Places <em>Spreading Smiles Across the Globe</em></h1>
        <p class="hero-lead">
            {{ config('app.name') }} crafts unforgettable journeys — with comfort, safety and trusted
            service across Bangladesh and beyond. Your perfect trip starts here.
        </p>
        <div class="hero-actions">
            <a href="#journeys" class="btn-x btn-x-gold"><i class="fa-solid fa-route"></i> Explore Journeys</a>
            <a href="#contact" class="btn-x btn-x-ghost"><i class="fa-solid fa-paper-plane"></i> Plan My Trip</a>
        </div>
        <ul class="hero-chips">
            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Visa support</li>
            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Hand-picked hotels</li>
            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> 24/7 assistance</li>
        </ul>
    </div>
    <a href="#about" class="hero-scroll" aria-label="Scroll to content"></a>
</section>
