<section class="cta-band" aria-labelledby="cta-title">
    <div class="container cta-inner">
        <div class="cta-copy" data-reveal>
            <h2 id="cta-title">{{ $site->section('cta', 'title') }} <em>{{ $site->section('cta', 'highlight') }}</em></h2>
            <p>{{ $site->section('cta', 'subtitle') }}</p>
        </div>
        <div class="cta-actions" data-reveal style="--d:.1s">
            <a href="https://wa.me/{{ config('custom.whatsapp_digits') }}" target="_blank" rel="noopener noreferrer" class="btn-x btn-x-wa"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
            <a href="#contact" class="btn-x btn-x-light"><i class="fa-solid fa-paper-plane"></i> Send an Enquiry</a>
        </div>
    </div>
</section>
