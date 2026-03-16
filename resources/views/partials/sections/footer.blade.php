@if(($variant ?? 'classic') === 'modern')
    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="#about">About</a>
                <a href="#services">Services</a>
                <a href="#gallery">Gallery</a>
                <a href="#reviews">Reviews</a>
                <a href="#contact">Contact</a>
            </div>
            <p class="footer-copy">
                © {{ date('Y') }} {{ config('app.name') }}.
                All Rights Reserved.
            </p>
        </div>
    </footer>
@else
    <div class="footer-bottom text-center py-4">
        <div class="container">

            <!-- Anchor Links -->
            <div class="footer-anchor-links mb-2">
                <a href="#about">About</a>
                <a href="#services">Services</a>
                <a href="#gallery">Gallery</a>
                <a href="#contact">Contact</a>
            </div>

            <!-- Copyright -->
            <small class="footer-copy">
                © {{ date('Y') }} {{ config('app.name') }}.
                All Rights Reserved.
            </small>

        </div>
    </div>
@endif
