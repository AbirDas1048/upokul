<section id="process" class="section section-alt" aria-labelledby="process-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">{{ $site->section('process', 'eyebrow') }}</span>
            <h2 class="sec-title" id="process-title">{{ $site->section('process', 'title') }} <em>{{ $site->section('process', 'highlight') }}</em></h2>
            <p class="sec-sub">{{ $site->section('process', 'subtitle') }}</p>
        </div>

        <ol class="process-grid" style="list-style:none;margin:0;padding:0">
            @foreach ($site->items('process_steps') as $step)
                <li class="step" data-reveal style="--d:{{ $loop->index * .1 }}s">
                    <div class="step-no" aria-hidden="true">{{ $loop->iteration }}</div>
                    <i class="fa-solid {{ preg_match('/^fa-[a-z0-9-]+$/', $step['icon'] ?? '') ? $step['icon'] : 'fa-check' }} step-ico" aria-hidden="true"></i>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['text'] ?? '' }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
