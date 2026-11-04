@php
    $steps = [
        ['fa-comments', 'Tell Us Your Plan', 'Share where, when and how you like to travel — a quick call or message is all it takes.'],
        ['fa-map-location-dot', 'Get a Tailored Itinerary', 'We design options with clear costs, so you can compare and choose with confidence.'],
        ['fa-passport', 'We Handle the Details', 'Visa guidance, flights, hotels and transfers — booked and confirmed for you.'],
        ['fa-suitcase-rolling', 'Travel With Support', 'Go and enjoy. Our team stays reachable around the clock if anything comes up.'],
    ];
@endphp

<section id="process" class="section section-alt" aria-labelledby="process-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">How It Works</span>
            <h2 class="sec-title" id="process-title">From First Hello to <em>Happy Return</em></h2>
            <p class="sec-sub">A simple, transparent process — four steps between you and your next adventure.</p>
        </div>

        <ol class="process-grid" style="list-style:none;margin:0;padding:0">
            @foreach ($steps as [$icon, $title, $text])
                <li class="step" data-reveal style="--d:{{ $loop->index * .1 }}s">
                    <div class="step-no" aria-hidden="true">{{ $loop->iteration }}</div>
                    <i class="fa-solid {{ $icon }} step-ico" aria-hidden="true"></i>
                    <h3>{{ $title }}</h3>
                    <p>{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
