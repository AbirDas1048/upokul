<section id="services" class="section section-alt" aria-labelledby="services-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">{{ $site->section('services', 'eyebrow') }}</span>
            <h2 class="sec-title" id="services-title">{{ $site->section('services', 'title') }} <em>{{ $site->section('services', 'highlight') }}</em></h2>
            <p class="sec-sub">{{ $site->section('services', 'subtitle') }}</p>
        </div>

        <div class="row g-4">
            @foreach ($site->items('services') as $service)
                <div class="col-md-6 col-xl-4" data-reveal style="--d:{{ ($loop->index % 3) * .1 }}s">
                    <article class="card-x service-card">
                        <div class="service-icon"><i class="fa-solid {{ preg_match('/^fa-[a-z0-9-]+$/', $service['icon'] ?? '') ? $service['icon'] : 'fa-star' }}" aria-hidden="true"></i></div>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['text'] ?? '' }}</p>
                        <a href="#contact" class="link-arrow" data-enquiry="{{ $service['subject'] ?? $service['title'] }}">Enquire now <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
