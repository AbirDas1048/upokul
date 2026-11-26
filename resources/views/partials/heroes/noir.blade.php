<section id="hero" class="hero hero-noir" aria-labelledby="demo2-hero-title">
    <img class="hero-img" src="{{ asset('images/banner.jpg') }}" alt="" fetchpriority="high">
    <div class="hero-overlay"></div>
    <div class="hero-orb orb-a" aria-hidden="true"></div>
    <div class="hero-orb orb-b" aria-hidden="true"></div>

    <div class="container hero-inner">
        <div class="hero-row">
            <div class="hero-copy hero-anim">
                <span class="hero-eyebrow"><i class="fa-solid fa-star" aria-hidden="true"></i> Trusted travel experts — Bangladesh &amp; beyond</span>
                <h1 id="demo2-hero-title">Discover Beautiful Places <em>Spreading Smiles Across the Globe</em></h1>
                <p class="hero-lead">
                    From first idea to final farewell, {{ config('app.name') }} designs refined, stress-free journeys —
                    personally planned and supported every step of the way.
                </p>
                <div class="hero-actions">
                    <a href="#services" class="btn-x btn-x-primary"><i class="fa-solid fa-route"></i> Our Services</a>
                    <a href="#gallery" class="btn-x btn-x-ghost"><i class="fa-solid fa-magnifying-glass-location"></i> Explore Places</a>
                </div>
            </div>

            <aside class="hero-aside" aria-label="Travel agency highlights">
                <div class="hero-card">
                    <h2>Travel, beautifully handled</h2>
                    <ul>
                        <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><span><b>Tailor-made itineraries</b>Planned around your pace and budget.</span></li>
                        <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><span><b>Visa, flights &amp; hotels</b>One team, one point of contact.</span></li>
                        <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><span><b>Support round the clock</b>Before, during and after your trip.</span></li>
                    </ul>
                    <a href="#contact" class="btn-x btn-x-primary w-100"><i class="fa-solid fa-paper-plane"></i> Plan My Trip</a>
                </div>
            </aside>
        </div>
    </div>

    <div class="hero-stats">
        <div class="container">
            <div class="hero-stats-inner">
                <div class="hero-stat"><b>100+</b><span>Satisfied Clients</span></div>
                <div class="hero-stat"><b>30+</b><span>Destinations</span></div>
                <div class="hero-stat"><b>800+</b><span>Hotel Partners</span></div>
                <div class="hero-stat"><b>24/7</b><span>Support</span></div>
            </div>
        </div>
    </div>
</section>
