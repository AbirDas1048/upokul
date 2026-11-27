@php
    $aside = $site->pairs('section.hero.aside_items', [
        ['Tailor-made itineraries', 'Planned around your pace and budget.'],
        ['Visa, flights & hotels', 'One team, one point of contact.'],
        ['Support round the clock', 'Before, during and after your trip.'],
    ]);
    $strip = $site->pairs('section.hero.strip');
@endphp
<section id="hero" class="hero hero-noir" aria-labelledby="demo2-hero-title">
    <img class="hero-img" src="{{ $site->image('banner') }}" alt="" fetchpriority="high">
    <div class="hero-overlay"></div>
    <div class="hero-orb orb-a" aria-hidden="true"></div>
    <div class="hero-orb orb-b" aria-hidden="true"></div>

    <div class="container hero-inner">
        <div class="hero-row">
            <div class="hero-copy hero-anim">
                <span class="hero-eyebrow"><i class="fa-solid fa-star" aria-hidden="true"></i> {{ $site->section('hero', 'eyebrow', 'Trusted travel experts — Bangladesh & beyond') }}</span>
                <h1 id="demo2-hero-title">{{ $site->section('hero', 'title', 'Discover Beautiful Places') }} <em>{{ $site->section('hero', 'highlight', 'Spreading Smiles Across the Globe') }}</em></h1>
                <p class="hero-lead">
                    {{ $site->section('hero', 'lead', 'From first idea to final farewell, '.config('app.name').' designs refined, stress-free journeys — personally planned and supported every step of the way.') }}
                </p>
                <div class="hero-actions">
                    <a href="#services" class="btn-x btn-x-primary"><i class="fa-solid fa-route"></i> {{ $site->section('hero', 'primary_label', 'Our Services') }}</a>
                    <a href="#gallery" class="btn-x btn-x-ghost"><i class="fa-solid fa-magnifying-glass-location"></i> {{ $site->section('hero', 'secondary_label', 'Explore Places') }}</a>
                </div>
            </div>

            <aside class="hero-aside" aria-label="Travel agency highlights">
                <div class="hero-card">
                    <h2>{{ $site->section('hero', 'aside_title', 'Travel, beautifully handled') }}</h2>
                    <ul>
                        @foreach ($aside as [$heading, $text])
                            <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i><span><b>{{ $heading }}</b>{{ $text }}</span></li>
                        @endforeach
                    </ul>
                    <a href="#contact" class="btn-x btn-x-primary w-100"><i class="fa-solid fa-paper-plane"></i> Plan My Trip</a>
                </div>
            </aside>
        </div>
    </div>

    @if ($strip)
        <div class="hero-stats">
            <div class="container">
                <div class="hero-stats-inner">
                    @foreach ($strip as [$number, $label])
                        <div class="hero-stat"><b>{{ $number }}</b><span>{{ $label }}</span></div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</section>
