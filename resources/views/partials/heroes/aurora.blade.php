<section id="hero" class="hero hero-aurora" style="background-image:url('{{ $site->image('banner') }}')" aria-labelledby="demo1-hero-title">
    <video class="hero-video" autoplay muted loop playsinline tabindex="-1" preload="metadata" poster="{{ $site->image('banner') }}" aria-hidden="true">
        <source src="{{ asset('videos/bg.mp4') }}" type="video/mp4">
        <track kind="captions" srclang="en" src="{{ asset('videos/captions.vtt') }}" label="English">
    </video>
    <div class="hero-overlay"></div>
    <button type="button" class="hero-video-toggle" id="heroVideoToggle" aria-pressed="false" aria-label="Pause background video">
        <i class="fa-solid fa-pause" aria-hidden="true"></i>
    </button>

    <div class="container hero-inner hero-anim">
        <span class="hero-eyebrow"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $site->section('hero', 'eyebrow', 'Travels & Holidays') }}</span>
        <h1 id="demo1-hero-title">{{ $site->section('hero', 'title', 'Discover Beautiful Places') }} <em>{{ $site->section('hero', 'highlight', 'Spreading Smiles Across the Globe') }}</em></h1>
        <p class="hero-lead">
            {{ $site->section('hero', 'lead', config('app.name').' crafts unforgettable journeys — with comfort, safety and trusted service across Bangladesh and beyond. Your perfect trip starts here.') }}
        </p>
        <div class="hero-actions">
            <a href="#journeys" class="btn-x btn-x-gold"><i class="fa-solid fa-route"></i> {{ $site->section('hero', 'primary_label', 'Explore Journeys') }}</a>
            <a href="#contact" class="btn-x btn-x-ghost"><i class="fa-solid fa-paper-plane"></i> {{ $site->section('hero', 'secondary_label', 'Plan My Trip') }}</a>
        </div>
        <ul class="hero-chips">
            @foreach ($site->lines('section.hero.chips') as $chip)
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> {{ $chip }}</li>
            @endforeach
        </ul>
    </div>
    <a href="#about" class="hero-scroll" aria-label="Scroll to content"></a>
</section>
