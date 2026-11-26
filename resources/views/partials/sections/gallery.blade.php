@php
    // Captions describe what each photograph actually shows.
    // Swap for your own destination photos when you have them.
    $photos = [
        ['1507525428034-b723cf961d3e', 'Sunset Shores', 'Beach Holidays', 'Sunset over a calm tropical beach'],
        ['1493558103817-58b2924bce98', 'Golden Coastlines', 'Beach Holidays', 'Sandy cove between sandstone cliffs'],
        ['1501785888041-af3ef285b470', 'Alpine Lakes', 'Mountain Escapes', 'Rowing boat on a turquoise mountain lake'],
        ['1476514525535-07fb3b4ae5f1', 'Lakeside Calm', 'Mountain Escapes', 'View from a wooden boat across a mountain lake'],
        ['1518548419970-58e3b4079ab2', 'Bali Sunsets', 'Indonesia', 'Temple on a sea rock at sunset in Bali'],
        ['1537996194471-e657df975ab4', 'Temples of Bali', 'Indonesia', 'Multi-tiered temple beside a lake in Bali'],
    ];
    $img = fn (string $id, int $w, int $q) => "https://images.unsplash.com/photo-{$id}?w={$w}&q={$q}&auto=format&fit=crop";
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
                            <img src="{{ $img($id, 800, 70) }}" alt="{{ $alt }}" width="800" height="600" loading="lazy" decoding="async">
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
                <div class="swiper-slide"><img src="{{ $img($id, 1400, 80) }}" alt="{{ $alt }}" loading="lazy"></div>
            @endforeach
        </div>
        <div class="swiper-button-prev" aria-label="Previous photo"></div>
        <div class="swiper-button-next" aria-label="Next photo"></div>
        <button type="button" class="close-modal" aria-label="Close gallery">&times;</button>
    </div>
</div>
