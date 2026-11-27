<section id="faq" class="section" aria-labelledby="faq-title">
    <div class="container">
        <div class="faq-grid">
            <div class="faq-aside" data-reveal>
                <span class="eyebrow">{{ $site->section('faq', 'eyebrow') }}</span>
                <h2 class="sec-title" id="faq-title">{{ $site->section('faq', 'title') }} <em>{{ $site->section('faq', 'highlight') }}</em></h2>
                <p>{{ $site->section('faq', 'subtitle') }}</p>
                <div class="faq-help">
                    <strong>Still curious?</strong>
                    <p>Chat with an expert — we usually reply quickly.</p>
                    <a href="https://wa.me/{{ config('custom.whatsapp_digits') }}" target="_blank" rel="noopener noreferrer" class="btn-x btn-x-primary btn-x-sm"><i class="fa-brands fa-whatsapp"></i> Ask on WhatsApp</a>
                </div>
            </div>

            <div class="faq-list" data-reveal style="--d:.1s">
                @foreach ($site->items('faqs') as $faq)
                    <details class="faq-item" @if ($loop->first) open @endif>
                        <summary>{{ $faq['title'] }} <i class="fa-solid fa-plus" aria-hidden="true"></i></summary>
                        <p>{{ $faq['answer'] ?? '' }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
</section>
