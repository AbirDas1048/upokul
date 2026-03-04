@if(($variant ?? 'classic') === 'modern')
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
@else
<section id="gallery" class="gallery-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title premium-section-title">Travel Moments</h2>
            <p class="text-muted premium-section-subtitle">Explore our unforgettable journeys</p>
        </div>

        <!-- CARD SLIDER -->
        <div class="swiper cardSwiper swiper-premium">
            <div class="swiper-wrapper">

                <!-- Card -->
                <div class="swiper-slide">
                    <div class="gallery-card" data-index="0">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Cox’s Bazar</h5>
                            <span>Bangladesh</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="1">
                        <img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Sylhet</h5>
                            <span>Bangladesh</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="2">
                        <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Bali</h5>
                            <span>Indonesia</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="0">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Cox’s Bazar</h5>
                            <span>Bangladesh</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="1">
                        <img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Sylhet</h5>
                            <span>Bangladesh</span>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="gallery-card" data-index="2">
                        <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=1200" alt="">
                        <div class="gallery-info">
                            <h5>Bali</h5>
                            <span>Indonesia</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pagination BELOW slider -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- FULLSCREEN MODAL -->
<div class="fullscreen-modal">
    <div class="swiper fullscreenSwiper">
        <div class="swiper-wrapper">

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98?w=2000" alt="">
            </div>

            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=2000" alt="">
            </div>

        </div>

        <!-- Arrows -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        <!-- Close -->
        <button class="close-modal">&times;</button>
    </div>
</div>
@endif
