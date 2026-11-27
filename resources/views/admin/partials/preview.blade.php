{{-- Needs: nothing. The site is same-origin, so it can be framed (CSP frame-src/frame-ancestors 'self'). --}}
<div data-preview-root>
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <select class="form-select form-select-sm w-auto ad-preview-select" data-preview-page aria-label="Page to preview">
            <option value="{{ url('/') }}?preview=draft">Draft: home page (with your unpublished changes)</option>
            <option value="{{ route('demo1') }}?preview=draft">Draft: Aurora design</option>
            <option value="{{ route('demo2') }}?preview=draft">Draft: Noir design</option>
            <option value="{{ route('demo3') }}?preview=draft">Draft: Glass design</option>
            <option value="{{ url('/') }}">Live: what visitors see now</option>
        </select>
        <div class="ad-device-bar" data-devices role="group" aria-label="Device size">
            <button type="button" class="active" data-device="desktop"><i class="fa-solid fa-desktop"></i> Desktop</button>
            <button type="button" data-device="tablet"><i class="fa-solid fa-tablet-screen-button"></i> Tablet</button>
            <button type="button" data-device="mobile"><i class="fa-solid fa-mobile-screen"></i> Phone</button>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-preview-reload><i class="fa-solid fa-rotate me-1"></i> Refresh</button>
        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm ms-auto"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open in new tab</a>
    </div>
    <div class="ad-frame-wrap">
        <iframe class="ad-frame" data-device="desktop" title="Website preview" loading="lazy"></iframe>
    </div>
</div>
