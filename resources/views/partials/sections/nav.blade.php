<header id="mainNav" class="site-nav">
    <div class="container nav-inner">

        <a href="#hero" class="nav-brand" aria-label="{{ config('app.name') }} — back to top">
            <span class="nav-logo"><img src="{{ asset('images/Logo.png') }}" alt="" width="44" height="44"></span>
            <span class="nav-name">{{ config('app.name') }}</span>
        </a>

        <nav class="nav-links" aria-label="Primary">
            <a href="#about">About</a>
            <a href="#services">Services</a>
            <a href="#journeys">Journeys</a>
            <a href="#gallery">Gallery</a>
            <a href="#reviews">Reviews</a>
            <a href="#faq">FAQ</a>
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
            <a href="#about" class="drawer-link">About</a>
            <a href="#services" class="drawer-link">Services</a>
            <a href="#journeys" class="drawer-link">Journeys</a>
            <a href="#process" class="drawer-link">How It Works</a>
            <a href="#gallery" class="drawer-link">Gallery</a>
            <a href="#reviews" class="drawer-link">Reviews</a>
            <a href="#faq" class="drawer-link">FAQ</a>
            <a href="#contact" class="btn-x btn-x-primary drawer-link"><i class="fa-solid fa-paper-plane"></i> Plan My Trip</a>
        </div>
    </div>
</header>
