<section id="about" class="section" aria-labelledby="about-title">
    <div class="container">
        <div class="about-grid">

            <div class="about-media" data-reveal>
                <div class="about-logo-card">
                    <img src="{{ $site->image('logo') }}" alt="{{ config('app.name') }} logo" width="420" height="420" loading="lazy" decoding="async">
                </div>
                <div class="about-tag"><i class="fa-solid fa-earth-asia" aria-hidden="true"></i> {{ $site->section('about', 'tag') }}</div>
            </div>

            <div class="about-copy" data-reveal style="--d:.1s">
                <span class="eyebrow">{{ $site->section('about', 'eyebrow') }}</span>
                <h2 class="sec-title" id="about-title">{{ $site->section('about', 'title') }} <em>{{ $site->section('about', 'highlight') }}</em></h2>
                <p>{{ $site->section('about', 'body') }}</p>

                <ul class="about-points">
                    @foreach ($site->items('about_points') as $point)
                        <li>
                            <span class="ico"><i class="fa-solid {{ preg_match('/^fa-[a-z0-9-]+$/', $point['icon'] ?? '') ? $point['icon'] : 'fa-circle-check' }}" aria-hidden="true"></i></span>
                            <div><strong>{{ $point['title'] }}</strong><span>{{ $point['text'] ?? '' }}</span></div>
                        </li>
                    @endforeach
                </ul>

                <div class="about-actions">
                    <a href="#trusted" class="btn-x btn-x-primary"><i class="fa-solid fa-award"></i> Why Choose Us</a>
                    <a href="#contact" class="btn-x btn-x-outline"><i class="fa-solid fa-comments"></i> Talk to an Expert</a>
                </div>
            </div>

        </div>
    </div>
</section>
