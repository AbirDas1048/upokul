<section id="reviews" class="section section-alt" aria-labelledby="reviews-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">{{ $site->section('reviews', 'eyebrow') }}</span>
            <h2 class="sec-title" id="reviews-title">{{ $site->section('reviews', 'title') }} <em>{{ $site->section('reviews', 'highlight') }}</em></h2>
            <p class="sec-sub">{{ $site->section('reviews', 'subtitle') }}</p>
        </div>

        <div class="swiper reviewSwiper" data-reveal>
            <div class="swiper-wrapper">
                @foreach ($site->items('reviews') as $review)
                    @php $source = ($review['source'] ?? 'google') === 'facebook' ? 'facebook' : 'google'; @endphp
                    <div class="swiper-slide">
                        <figure class="card-x review-card m-0">
                            <div class="review-stars"><span aria-hidden="true">★★★★★</span><span class="visually-hidden-x">5 out of 5 stars</span></div>
                            <blockquote class="review-quote">“{{ $review['quote'] ?? '' }}”</blockquote>
                            <figcaption class="review-author">
                                <span class="avatar" aria-hidden="true">{{ mb_substr($review['title'], 0, 1) }}</span>
                                <span>
                                    <strong>{{ $review['title'] }}</strong>
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
            @if ($url = $site->text('contact.google_review_url'))
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="btn-review">
                    <i class="fa-brands fa-google" aria-hidden="true"></i> Review us on Google
                </a>
            @endif
            @if ($url = $site->text('contact.facebook_review_url'))
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="btn-review fb">
                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i> Review us on Facebook
                </a>
            @endif
        </div>
    </div>
</section>
