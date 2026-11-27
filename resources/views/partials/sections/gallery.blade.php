@php
    // Photos are managed under Website content → Gallery (the shipped ones live in public/images/destinations).
    $photos = $site->items('gallery')->filter(fn ($p) => ! empty($p['image_url']))->values();
@endphp

<section id="gallery" class="section gallery-section" aria-labelledby="gallery-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">{{ $site->section('gallery', 'eyebrow') }}</span>
            <h2 class="sec-title" id="gallery-title">{{ $site->section('gallery', 'title') }} <em>{{ $site->section('gallery', 'highlight') }}</em></h2>
            <p class="sec-sub">{{ $site->section('gallery', 'subtitle') }}</p>
        </div>

        <div class="swiper cardSwiper" data-reveal>
            <div class="swiper-wrapper">
                @foreach ($photos as $i => $photo)
                    <div class="swiper-slide">
                        <button type="button" class="gallery-card" data-index="{{ $i }}" aria-label="View {{ $photo['title'] }} full screen">
                            <img src="{{ $photo['image_url'] }}" alt="{{ $photo['alt'] ?? $photo['title'] }}" width="800" height="600" loading="lazy" decoding="async">
                            <span class="gallery-expand" aria-hidden="true"><i class="fa-solid fa-expand"></i></span>
                            <span class="gallery-info"><strong class="gallery-title">{{ $photo['title'] }}</strong><small>{{ $photo['category'] ?? '' }}</small></span>
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
            @foreach ($photos as $photo)
                <div class="swiper-slide"><img src="{{ $photo['full_url'] }}" alt="{{ $photo['alt'] ?? $photo['title'] }}" loading="lazy"></div>
            @endforeach
        </div>
        <div class="swiper-button-prev" aria-label="Previous photo"></div>
        <div class="swiper-button-next" aria-label="Next photo"></div>
        <button type="button" class="close-modal" aria-label="Close gallery">&times;</button>
    </div>
</div>
