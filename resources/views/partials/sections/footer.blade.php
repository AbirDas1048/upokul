<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">

            <div>
                <a href="#hero" class="footer-brand" aria-label="{{ config('app.name') }} — back to top">
                    <span class="nav-logo"><img src="{{ $site->image('logo') }}" alt="" width="44" height="44" loading="lazy"></span>
                    <span>{{ config('app.name') }}</span>
                </a>
                <p class="footer-about">{{ $site->section('footer', 'about') }}</p>
                <div class="social">
                    <a href="{{ config('custom.facebook_page_link') }}" target="_blank" rel="noopener noreferrer" aria-label="{{ config('custom.facebook_page_name') }} on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://wa.me/{{ config('custom.whatsapp_digits') }}" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="mailto:{{ config('custom.office_email') }}" aria-label="Email us"><i class="fa-solid fa-envelope"></i></a>
                </div>
            </div>

            <nav aria-label="Quick links">
                <h2 class="footer-title">Explore</h2>
                <ul class="footer-links">
                    @if ($site->enabled('about')) <li><a href="#about">About Us</a></li> @endif
                    @if ($site->enabled('journeys')) <li><a href="#journeys">Journeys</a></li> @endif
                    @if ($site->enabled('process')) <li><a href="#process">How It Works</a></li> @endif
                    @if ($site->enabled('gallery')) <li><a href="#gallery">Gallery</a></li> @endif
                    @if ($site->enabled('reviews')) <li><a href="#reviews">Reviews</a></li> @endif
                    @if ($site->enabled('faq')) <li><a href="#faq">FAQ</a></li> @endif
                </ul>
            </nav>

            <nav aria-label="Services">
                <h2 class="footer-title">Services</h2>
                <ul class="footer-links">
                    @foreach ($site->items('services')->take(8) as $service)
                        <li><a href="#services">{{ $service['title'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <div>
                <h2 class="footer-title">Contact</h2>
                <ul class="footer-links footer-contact">
                    <li><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>{{ config('custom.office_address') }}</span></li>
                    <li><i class="fa-solid fa-phone" aria-hidden="true"></i><a href="tel:{{ config('custom.office_phone_tel') }}">{{ config('custom.office_phone') }}</a></li>
                    <li><i class="fa-solid fa-envelope" aria-hidden="true"></i><a href="mailto:{{ config('custom.office_email') }}">{{ config('custom.office_email') }}</a></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <span>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
            <a href="#hero">Back to top <i class="fa-solid fa-arrow-up" aria-hidden="true"></i></a>
        </div>
    </div>
</footer>
