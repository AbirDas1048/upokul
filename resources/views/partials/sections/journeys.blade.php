@php
    $unsplash = fn (string $id) => "https://images.unsplash.com/photo-{$id}?w=800&q=70&auto=format&fit=crop";
    $journeys = [
        ['Domestic', 'Escapes in Bangladesh', $unsplash('1507525428034-b723cf961d3e'), 'Beach, hills & heritage getaways', ['Coastal & hill-country escapes', 'Comfortable transport & stays', 'Weekend to week-long plans'], 'Domestic tour'],
        ['International', 'World Tours', $unsplash('1537996194471-e657df975ab4'), 'Handled start to finish', ['Visa & documentation guidance', 'Flights, hotels & transfers', 'Local-expert recommendations'], 'International tour'],
        ['Couples', 'Honeymoon Retreats', $unsplash('1518548419970-58e3b4079ab2'), 'Romantic, private, effortless', ['Sunset-view stays', 'Private transfers & experiences', 'Surprise-ready planning'], 'Honeymoon package'],
        ['Groups', 'Family & Group Holidays', $unsplash('1493558103817-58b2924bce98'), 'Fun for every age', ['Flexible, kid-friendly pacing', 'Group-friendly rooms & transport', 'One point of contact for all'], 'Family / group holiday'],
    ];
@endphp

<section id="journeys" class="section" aria-labelledby="journeys-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">Signature Journeys</span>
            <h2 class="sec-title" id="journeys-title">Find the Trip That <em>Feels Like You</em></h2>
            <p class="sec-sub">Start from a style of travel — we'll shape the details, dates and budget together.</p>
        </div>

        <div class="journey-grid">
            @foreach ($journeys as [$tag, $title, $img, $alt, $points, $subject])
                <article class="journey" data-reveal style="--d:{{ $loop->index * .08 }}s">
                    <img src="{{ $img }}" alt="" width="800" height="1000" loading="lazy" decoding="async">
                    <div class="journey-body">
                        <span class="journey-tag">{{ $tag }}</span>
                        <h3>{{ $title }}</h3>
                        <p class="journey-sub">{{ $alt }}</p>
                        <ul>
                            @foreach ($points as $point)
                                <li><i class="fa-solid fa-circle" aria-hidden="true"></i><span>{{ $point }}</span></li>
                            @endforeach
                        </ul>
                        <a href="#contact" class="link-arrow" data-enquiry="{{ $subject }}">Plan this trip <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
