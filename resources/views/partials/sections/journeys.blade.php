<section id="journeys" class="section" aria-labelledby="journeys-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">{{ $site->section('journeys', 'eyebrow') }}</span>
            <h2 class="sec-title" id="journeys-title">{{ $site->section('journeys', 'title') }} <em>{{ $site->section('journeys', 'highlight') }}</em></h2>
            <p class="sec-sub">{{ $site->section('journeys', 'subtitle') }}</p>
        </div>

        <div class="journey-grid">
            @foreach ($site->items('journeys') as $journey)
                <article class="journey" data-reveal style="--d:{{ $loop->index * .08 }}s">
                    @if ($journey['image_url'])
                        <img src="{{ $journey['image_url'] }}" alt="" width="800" height="1000" loading="lazy" decoding="async">
                    @endif
                    <div class="journey-body">
                        <span class="journey-tag">{{ $journey['tag'] ?? '' }}</span>
                        <h3>{{ $journey['title'] }}</h3>
                        <p class="journey-sub">{{ $journey['subtitle'] ?? '' }}</p>
                        <ul>
                            @foreach ($journey['points_list'] ?? [] as $point)
                                <li><i class="fa-solid fa-circle" aria-hidden="true"></i><span>{{ $point }}</span></li>
                            @endforeach
                        </ul>
                        <a href="#contact" class="link-arrow" data-enquiry="{{ $journey['subject'] ?? $journey['title'] }}">Plan this trip <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
