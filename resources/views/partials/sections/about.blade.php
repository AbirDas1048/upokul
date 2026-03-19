@if(($variant ?? 'classic') === 'modern')
    <section id="about">
        <div class="container">
            <div class="about-grid">

                <!-- Image -->
                <div class="about-img-side" data-reveal>
                    <div class="about-img-frame">
                        <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=700&q=65&auto=format" alt="About Upokul Travels">
                    </div>
                    <div class="about-badge-card">
                        <div class="badge-big">10+</div>
                        <div class="badge-txt">
                            <strong>Years</strong>
                            of Excellence
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div data-reveal style="transition-delay:.02s">
                    <span class="section-label">About Us</span>
                    <h2 class="section-title" style="margin-bottom:1rem">
                        Your Trusted Partner for
                        <span>Unforgettable Journeys</span>
                    </h2>
                    <p style="font-size:.9rem;color:var(--muted);line-height:1.85;margin-bottom:1.8rem">
                        Upokul Travels is dedicated to creating meaningful travel experiences.
                        From breathtaking coastal tours to personalized holiday packages,
                        we ensure comfort, safety, and unforgettable memories for every traveler.
                    </p>
                    <ul class="about-list">
                        <li><i class="fas fa-check-circle"></i><span>Trusted & experienced travel experts</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Customized tour packages for every budget</span></li>
                        <li><i class="fas fa-check-circle"></i><span>24/7 customer support throughout your journey</span></li>
                    </ul>
                    <a href="#trusted" class="btn btn-primary">
                        <i class="fas fa-award"></i> Why Choose Us?
                    </a>
                </div>

            </div>
        </div>
    </section>
@else
    <section id="about" class="about-section">
        <div class="container">
            <div class="row align-items-center gy-5">

                <!-- Image -->
                <div class="col-lg-6" data-reveal>
                    <div class="about-image-wrapper">
                        <img src="{{ asset('images/Logo.png') }}" alt="About {{ config('app.name') }}">
                    </div>
                </div>

                <!-- Content -->
                <div class="col-lg-6" data-reveal>
                    <div class="about-content">
                        <h4 class="about-badge">About Us</h4>

                        <h2>
                            Your Trusted Partner for
                            <span>Unforgettable Journeys</span>
                        </h2>

                        <p>
                            Upokul Travels is dedicated to creating meaningful travel experiences.
                            From breathtaking coastal tours to personalized holiday packages,
                            we ensure comfort, safety, and unforgettable memories.
                        </p>

                        <ul class="about-list">
                            <li><i class="fas fa-check-circle"></i><span>Trusted & experienced travel experts</span></li>
                            <li><i class="fas fa-check-circle"></i><span>Customized tour packages</span></li>
                            <li><i class="fas fa-check-circle"></i><span>24/7 customer support</span></li>
                        </ul>

                        <a href="#trusted" class="btn premium-btn premium-btn-pill premium-btn-glossy-primary mt-3" data-reveal>
                            Why Choose Us?
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endif
