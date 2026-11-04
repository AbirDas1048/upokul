@php
    $services = [
        ['fa-route', 'Tour Planning', 'Custom itineraries built around your pace, budget and interests — domestic escapes to multi-country trips.', 'Tour Planning'],
        ['fa-hotel', 'Hotel Booking', 'Hand-picked stays at fair rates, chosen for comfort, safety and location.', 'Hotel Booking'],
        ['fa-plane-departure', 'Air Ticket Booking', 'Flexible, competitively priced flights with trusted airlines worldwide.', 'Air Ticket Booking'],
        ['fa-passport', 'Visa Processing', 'Clear guidance on documents and timelines, with expert help at every step.', 'Visa Processing'],
        ['fa-van-shuttle', 'Transport & Transfers', 'Safe, comfortable airport pick-ups, city transfers and tour transport.', 'Transport & Transfers'],
        ['fa-shield-heart', 'Travel Insurance', 'Dependable cover so you can travel with real peace of mind.', 'Travel Insurance'],
    ];
@endphp

<section id="services" class="section section-alt" aria-labelledby="services-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">Our Services</span>
            <h2 class="sec-title" id="services-title">Everything You Need for a <em>Perfect Journey</em></h2>
            <p class="sec-sub">One team for every step — planning, booking, paperwork and on-trip support.</p>
        </div>

        <div class="row g-4">
            @foreach ($services as [$icon, $title, $text, $subject])
                <div class="col-md-6 col-xl-4" data-reveal style="--d:{{ ($loop->index % 3) * .1 }}s">
                    <article class="card-x service-card">
                        <div class="service-icon"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></div>
                        <h3>{{ $title }}</h3>
                        <p>{{ $text }}</p>
                        <a href="#contact" class="link-arrow" data-enquiry="{{ $subject }}">Enquire now <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
