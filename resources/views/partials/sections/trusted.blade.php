<section id="trusted" class="trust" aria-labelledby="trusted-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">{{ $site->section('trusted', 'eyebrow') }}</span>
            <h2 class="sec-title" id="trusted-title">{{ $site->section('trusted', 'title') }} <em>{{ $site->section('trusted', 'highlight') }}</em></h2>
            <p class="sec-sub">{{ $site->section('trusted', 'subtitle') }}</p>
        </div>

        <div class="trust-grid">
            @foreach ($site->items('stats') as $stat)
                <div class="trust-card" data-reveal style="--d:{{ $loop->index * .08 }}s">
                    <i class="fa-solid {{ preg_match('/^fa-[a-z0-9-]+$/', $stat['icon'] ?? '') ? $stat['icon'] : 'fa-star' }}" aria-hidden="true"></i>
                    <div class="trust-num counter" data-count="{{ (int) ($stat['count'] ?? 0) }}" data-suffix="{{ $stat['suffix'] ?? '' }}">{{ (int) ($stat['count'] ?? 0) }}</div>
                    <p>{{ $stat['title'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
