@php
    $faqs = [
        ['How early should I plan my trip?', 'The earlier the better — it widens your choice of flights, hotels and dates, especially in peak seasons and for visa-dependent destinations. That said, talk to us whenever you are ready and we will tell you honestly what is possible.'],
        ['Can you help with visa processing?', 'Yes. We guide you on the documents required, help you prepare your application and keep you updated on progress. Requirements and timelines vary by country, so we confirm them with you at the start.'],
        ['Can I customize a package?', 'Absolutely. Every trip is built around you — destinations, hotel style, pace, activities and budget. Tell us what matters and we will shape an itinerary to match.'],
        ['Do you arrange domestic as well as international trips?', 'Yes. We plan escapes within Bangladesh as well as international holidays, including honeymoon, family and group travel.'],
        ['What if I need help while I am travelling?', 'Our team is reachable around the clock. Message us on WhatsApp or call the office and a real person will help you sort it out.'],
        ['How do I get a quote?', 'Send us a message using the form below or on WhatsApp with your destination, rough dates and number of travelers. We will come back with clear options and costs.'],
    ];
@endphp

<section id="faq" class="section" aria-labelledby="faq-title">
    <div class="container">
        <div class="faq-grid">
            <div class="faq-aside" data-reveal>
                <span class="eyebrow">FAQ</span>
                <h2 class="sec-title" id="faq-title">Questions, <em>Answered</em></h2>
                <p>Everything travelers usually ask before booking. Can't find yours? We're happy to help.</p>
                <div class="faq-help">
                    <strong>Still curious?</strong>
                    <p>Chat with an expert — we usually reply quickly.</p>
                    <a href="https://wa.me/{{ config('custom.whatsapp_digits') }}" target="_blank" rel="noopener noreferrer" class="btn-x btn-x-primary btn-x-sm"><i class="fa-brands fa-whatsapp"></i> Ask on WhatsApp</a>
                </div>
            </div>

            <div class="faq-list" data-reveal style="--d:.1s">
                @foreach ($faqs as [$q, $a])
                    <details class="faq-item" @if ($loop->first) open @endif>
                        <summary>{{ $q }} <i class="fa-solid fa-plus" aria-hidden="true"></i></summary>
                        <p>{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
</section>
