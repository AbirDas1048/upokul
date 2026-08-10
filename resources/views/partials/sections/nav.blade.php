<nav id="mainNav" class="navbar navbar-expand-lg fixed-top">
    <div class="container">

        <div class="nav-inner w-100">

            <!-- Brand -->
            <a href="#hero" class="nav-brand d-flex align-items-center">
                <div class="nav-brand-icon">
                    <img src="{{ asset('images/Logo.png') }}" alt="{{ config('app.name') }}">
                </div>
                <span class="nav-brand-text">{{ config('app.name') }}<em>.</em></span>
            </a>

            <!-- Desktop Menu -->
            <ul class="nav-links d-none d-lg-flex">
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#gallery">Gallery</a></li>
                <li><a href="#reviews">Reviews</a></li>
            </ul>

            <div class="nav-right d-flex align-items-center gap-2">
                <!-- Desktop only -->
                <a href="#contact" class="btn-nav d-none d-lg-inline-flex">Contact</a>

                <!-- Mobile only -->
                <button class="hamburger d-lg-none" id="ham" aria-expanded="false" aria-label="Toggle menu" aria-controls="drawer">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
            </div>

        </div>

        <!-- Mobile Drawer -->
        <div class="nav-drawer" id="drawer">
            <div class="nav-drawer-links">
                <a href="#about" class="drawer-link">About</a>
                <a href="#services" class="drawer-link">Services</a>
                <a href="#gallery" class="drawer-link">Gallery</a>
                <a href="#reviews" class="drawer-link">Reviews</a>
            </div>
            <a href="#contact" class="btn-nav drawer-link">Contact</a>
        </div>

    </div>
</nav>
