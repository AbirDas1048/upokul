<header id="mainNav" class="site-nav">
    <div class="container nav-inner">

        <a href="#hero" class="nav-brand" aria-label="{{ config('app.name') }} — back to top">
            <span class="nav-logo"><img src="{{ $site->image('logo') }}" alt="" width="44" height="44"></span>
            <span class="nav-name">{{ config('app.name') }}</span>
        </a>

        <nav class="nav-links" aria-label="Primary">
            @if ($site->enabled('about')) <a href="#about">About</a> @endif
            @if ($site->enabled('services')) <a href="#services">Services</a> @endif
            @if ($site->enabled('journeys')) <a href="#journeys">Journeys</a> @endif
            @if ($site->enabled('gallery')) <a href="#gallery">Gallery</a> @endif
            @if ($site->enabled('reviews')) <a href="#reviews">Reviews</a> @endif
            @if ($site->enabled('faq')) <a href="#faq">FAQ</a> @endif
        </nav>

        <div class="nav-actions">
            <a href="#contact" class="btn-x btn-x-primary btn-x-sm nav-cta"><i class="fa-solid fa-paper-plane"></i> Plan My Trip</a>
            <button type="button" class="nav-toggle" id="ham" aria-expanded="false" aria-controls="drawer" aria-label="Open menu">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="nav-drawer" id="drawer">
        <div class="container">
            @if ($site->enabled('about')) <a href="#about" class="drawer-link">About</a> @endif
            @if ($site->enabled('services')) <a href="#services" class="drawer-link">Services</a> @endif
            @if ($site->enabled('journeys')) <a href="#journeys" class="drawer-link">Journeys</a> @endif
            @if ($site->enabled('process')) <a href="#process" class="drawer-link">How It Works</a> @endif
            @if ($site->enabled('gallery')) <a href="#gallery" class="drawer-link">Gallery</a> @endif
            @if ($site->enabled('reviews')) <a href="#reviews" class="drawer-link">Reviews</a> @endif
            @if ($site->enabled('faq')) <a href="#faq" class="drawer-link">FAQ</a> @endif
            <a href="#contact" class="btn-x btn-x-primary drawer-link"><i class="fa-solid fa-paper-plane"></i> Plan My Trip</a>
        </div>
    </div>
</header>
