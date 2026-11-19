@php
    // Photos are defined in config/destinations.php and served from public/images/destinations.
    $photos = [];
    foreach (config('destinations.photos') as $slug => $p) {
        $photos[] = [$slug, $p['title'], $p['category'], $p['alt']];
    }
    $img = fn (string $slug, int $w) => asset(config('destinations.path')."/{$slug}-{$w}.jpg");
@endphp

<section id="gallery" class="section gallery-section" aria-labelledby="gallery-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">Travel Moments</span>
            <h2 class="sec-title" id="gallery-title">Explore Our <em>Destinations</em></h2>
            <p class="sec-sub">A glimpse of the places our travelers fall in love with. Tap any photo to view it full screen.</p>
        </div>

        <div class="swiper cardSwiper" data-reveal>
            <div class="swiper-wrapper">
                @foreach ($photos as $i => [$id, $title, $sub, $alt])
                    <div class="swiper-slide">
                        <button type="button" class="gallery-card" data-index="{{ $i }}" aria-label="View {{ $title }} full screen">
                            <img src="{{ $img($id, 800) }}" alt="{{ $alt }}" width="800" height="600" loading="lazy" decoding="async">
                            <span class="gallery-expand" aria-hidden="true"><i class="fa-solid fa-expand"></i></span>
                            <span class="gallery-info"><strong class="gallery-title">{{ $title }}</strong><small>{{ $sub }}</small></span>
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<div class="fullscreen-modal" id="galleryModal" role="dialog" aria-modal="true" aria-label="Image gallery" aria-hidden="true">
    <div class="swiper fullscreenSwiper">
        <div class="swiper-wrapper">
            @foreach ($photos as [$id, $title, $sub, $alt])
                <div class="swiper-slide"><img src="{{ $img($id, 1400) }}" alt="{{ $alt }}" loading="lazy"></div>
            @endforeach
        </div>
        <div class="swiper-button-prev" aria-label="Previous photo"></div>
        <div class="swiper-button-next" aria-label="Next photo"></div>
        <button type="button" class="close-modal" aria-label="Close gallery">&times;</button>
    </div>
</div>
