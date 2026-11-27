@php
    $asideIcons = ['fa-wand-magic-sparkles', 'fa-passport', 'fa-headset', 'fa-star', 'fa-heart'];
    $aside = $site->pairs('section.hero.aside_items', [
        ['Made for you', 'Itineraries shaped around your style.'],
        ['Paperwork, handled', 'Visa guidance, flights & hotels.'],
        ['Always reachable', '24/7 support wherever you are.'],
    ]);
@endphp
<section id="hero" class="hero hero-glass" style="background-image:url('{{ $site->image('banner') }}')" aria-labelledby="demo3-hero-title">
    <video class="hero-video" autoplay muted loop playsinline tabindex="-1" preload="metadata" poster="{{ $site->image('banner') }}" aria-hidden="true">
        <source src="{{ asset('videos/bg.mp4') }}" type="video/mp4">
        <track kind="captions" srclang="en" src="{{ asset('videos/captions.vtt') }}" label="English">
    </video>
    <div class="hero-overlay"></div>
    <button type="button" class="hero-video-toggle" id="heroVideoToggle" aria-pressed="false" aria-label="Pause background video">
        <i class="fa-solid fa-pause" aria-hidden="true"></i>
    </button>

    <div class="container hero-inner">
        <div class="hero-row">
            <div class="hero-copy hero-anim">
                <span class="hero-eyebrow"><i class="fa-solid fa-gem" aria-hidden="true"></i> {{ $site->section('hero', 'eyebrow', 'Premium travel, personally planned') }}</span>
                <h1 id="demo3-hero-title">{{ $site->section('hero', 'title', 'Discover Beautiful Places') }} <em>{{ $site->section('hero', 'highlight', 'Spreading Smiles Across the Globe') }}</em></h1>
                <p class="hero-lead">
                    {{ $site->section('hero', 'lead', 'We create unforgettable travel experiences with comfort, safety and trusted service — across Bangladesh and the world.') }}
                </p>
                <div class="hero-actions">
                    <a href="#services" class="btn-x btn-x-gold"><i class="fa-solid fa-route"></i> {{ $site->section('hero', 'primary_label', 'Our Services') }}</a>
                    <a href="#gallery" class="btn-x btn-x-ghost"><i class="fa-solid fa-magnifying-glass-location"></i> {{ $site->section('hero', 'secondary_label', 'Explore Places') }}</a>
                </div>
            </div>

            <aside class="hero-aside" aria-label="Why travelers choose us">
                <div class="hero-card">
                    <h2>{{ $site->section('hero', 'aside_title', 'Why travelers choose us') }}</h2>
                    <ul>
                        @foreach ($aside as $i => [$heading, $text])
                            <li><span class="ico"><i class="fa-solid {{ $asideIcons[$i % count($asideIcons)] }}" aria-hidden="true"></i></span><span><b>{{ $heading }}</b>{{ $text }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
    <a href="#about" class="hero-scroll" aria-label="Scroll to content"></a>
</section>
