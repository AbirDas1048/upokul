<section id="hero" class="hero hero-glass" style="background-image:url('{{ asset('images/banner.jpg') }}')" aria-labelledby="demo3-hero-title">
    <video class="hero-video" autoplay muted loop playsinline tabindex="-1" preload="metadata" poster="{{ asset('images/banner.jpg') }}" aria-hidden="true">
        <source src="{{ asset('videos/bg.mp4') }}" type="video/mp4">
        <track kind="captions" srclang="en" src="{{ asset('videos/captions.vtt') }}" label="English">
    </video>
    <div class="hero-overlay"></div>

    <div class="container hero-inner">
        <div class="hero-row">
            <div class="hero-copy hero-anim">
                <span class="hero-eyebrow"><i class="fa-solid fa-gem" aria-hidden="true"></i> Premium travel, personally planned</span>
                <h1 id="demo3-hero-title">Discover Beautiful Places <em>Spreading Smiles Across the Globe</em></h1>
                <p class="hero-lead">
                    We create unforgettable travel experiences with comfort, safety and trusted service —
                    across Bangladesh and the world.
                </p>
                <div class="hero-actions">
                    <a href="#services" class="btn-x btn-x-gold"><i class="fa-solid fa-route"></i> Our Services</a>
                    <a href="#gallery" class="btn-x btn-x-ghost"><i class="fa-solid fa-magnifying-glass-location"></i> Explore Places</a>
                </div>
            </div>

            <aside class="hero-aside" aria-label="Why travelers choose us">
                <div class="hero-card">
                    <h2>Why travelers choose us</h2>
                    <ul>
                        <li><span class="ico"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span><span><b>Made for you</b>Itineraries shaped around your style.</span></li>
                        <li><span class="ico"><i class="fa-solid fa-passport" aria-hidden="true"></i></span><span><b>Paperwork, handled</b>Visa guidance, flights &amp; hotels.</span></li>
                        <li><span class="ico"><i class="fa-solid fa-headset" aria-hidden="true"></i></span><span><b>Always reachable</b>24/7 support wherever you are.</span></li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
    <a href="#about" class="hero-scroll" aria-label="Scroll to content"></a>
</section>
