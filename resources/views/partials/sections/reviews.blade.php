@php
    $reviews = [
        ['Rahim Ahmed', 'google', 'Amazing service! Visa processing and hotel booking were handled so smoothly. Highly recommended to everyone.'],
        ['Nusrat Jahan', 'google', 'Very professional team. Our family tour was perfectly planned and completely stress-free. Will book again!'],
        ['Tanvir Hasan', 'google', 'Best travel agency in Bangladesh. Quick support, transparent pricing, and they genuinely care about travelers.'],
        ['Farida Begum', 'facebook', 'They handled everything from flights to hotels perfectly. The whole trip was magical — absolutely loved it!'],
        ['Karim & Sadia', 'facebook', 'Exceptional experience. The team was attentive and made our honeymoon absolutely unforgettable.'],
    ];
@endphp

<section id="reviews" class="section section-alt" aria-labelledby="reviews-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">Testimonials</span>
            <h2 class="sec-title" id="reviews-title">What Our Travelers <em>Say</em></h2>
            <p class="sec-sub">Kind words from our Google Business and Facebook pages.</p>
        </div>

        <div class="swiper reviewSwiper" data-reveal>
            <div class="swiper-wrapper">
                @foreach ($reviews as [$name, $source, $quote])
                    <div class="swiper-slide">
                        <figure class="card-x review-card m-0">
                            <div class="review-stars" role="img" aria-label="5 out of 5 stars">★★★★★</div>
                            <blockquote class="review-quote">“{{ $quote }}”</blockquote>
                            <figcaption class="review-author">
                                <span class="avatar" aria-hidden="true">{{ mb_substr($name, 0, 1) }}</span>
                                <span>
                                    <strong>{{ $name }}</strong>
                                    <span class="review-src">
                                        <i class="fa-brands fa-{{ $source }}" aria-hidden="true"></i>
                                        {{ ucfirst($source) }} review
                                    </span>
                                </span>
                            </figcaption>
                        </figure>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <div class="review-actions">
            <a href="https://search.google.com/local/writereview?placeid=ChIJb5Q_kd_ZrDARzdblKHT4xYo" target="_blank" rel="noopener noreferrer" class="btn-review">
                <i class="fa-brands fa-google" aria-hidden="true"></i> Review us on Google
            </a>
            <a href="https://www.facebook.com/profile.php?id=61555938637948&sk=reviews" target="_blank" rel="noopener noreferrer" class="btn-review fb">
                <i class="fa-brands fa-facebook-f" aria-hidden="true"></i> Review us on Facebook
            </a>
        </div>
    </div>
</section>
