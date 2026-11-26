@php
    $stats = [
        ['fa-face-smile-beam', 100, '+', 'Satisfied Clients'],
        ['fa-earth-asia', 30, '+', 'Destinations Covered'],
        ['fa-hotel', 800, '+', 'Hotel Partnerships'],
        ['fa-handshake', 500, '+', 'Trusted Agents & Media'],
    ];
@endphp

<section id="trusted" class="trust" aria-labelledby="trusted-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">Why Travelers Choose Us</span>
            <h2 class="sec-title" id="trusted-title">Trusted by Travelers <em>Everywhere</em></h2>
            <p class="sec-sub">Numbers that reflect our experience, our partnerships and the trust placed in us.</p>
        </div>

        <div class="trust-grid">
            @foreach ($stats as [$icon, $count, $suffix, $label])
                <div class="trust-card" data-reveal style="--d:{{ $loop->index * .08 }}s">
                    <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                    <div class="trust-num counter" data-count="{{ $count }}" data-suffix="{{ $suffix }}">{{ $count }}</div>
                    <p>{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
