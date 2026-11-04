<section id="contact" class="section section-alt" aria-labelledby="contact-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">Get In Touch</span>
            <h2 class="sec-title" id="contact-title">Let's Plan Your <em>Next Trip</em></h2>
            <p class="sec-sub">Send us a few details and our team will get back to you with options and a clear quote.</p>
        </div>

        <div class="contact-grid">

            <div class="card-x contact-card" data-reveal>
                <h3>Send a Message</h3>
                <form id="contactForm" method="POST" action="{{ route('contact.submit') }}" novalidate>
                    @csrf
                    <div class="field-row">
                        <div class="field">
                            <label for="name">Your name</label>
                            <input type="text" name="name" id="name" placeholder="e.g. Rahim Ahmed" autocomplete="name" maxlength="100" required>
                        </div>
                        <div class="field">
                            <label for="phone">Phone / WhatsApp</label>
                            <input type="tel" name="phone" id="phone" placeholder="+8801XXXXXXXXX" autocomplete="tel" inputmode="tel" maxlength="20" required>
                        </div>
                    </div>
                    <div class="field">
                        <label for="email">Email address</label>
                        <input type="email" name="email" id="email" placeholder="you@example.com" autocomplete="email" maxlength="150" required>
                    </div>
                    <div class="field">
                        <label for="subject">Interested in</label>
                        <input type="text" name="subject" id="subject" placeholder="e.g. Honeymoon package, Visa processing" maxlength="100" required>
                    </div>
                    <div class="field">
                        <label for="mail_message">Your message</label>
                        <textarea rows="5" name="mail_message" id="mail_message" placeholder="Destination, travel dates, number of travelers…" maxlength="2000" required></textarea>
                    </div>
                    <button type="submit" class="btn-x btn-x-primary" id="submitBtn">
                        <i class="fa-solid fa-paper-plane"></i> Send Message
                    </button>
                    <p class="form-note"><i class="fa-solid fa-lock" aria-hidden="true"></i> Your details are only used to reply to your enquiry.</p>
                </form>
            </div>

            <div class="card-x contact-card" data-reveal style="--d:.1s">
                <div class="map-box">
                    <iframe
                        title="{{ config('app.name') }} office location on Google Maps"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3692.600173850267!2d91.81103287529328!3d22.325913979668194!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30acd9df913f946f%3A0x8ac5f87428e5d6cd!2sUpokul%20Travels%20%26%20Holidays!5e0!3m2!1sen!2sbd!4v1768747684228!5m2!1sen!2sbd"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>

                <ul class="info-list">
                    <li>
                        <span class="ico"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                        <div><small>Office</small><span class="v">{{ config('custom.office_address') }}</span></div>
                    </li>
                    <li>
                        <span class="ico"><i class="fa-solid fa-phone" aria-hidden="true"></i></span>
                        <div><small>Call us</small><a href="tel:{{ config('custom.office_phone_tel') }}">{{ config('custom.office_phone') }}</a></div>
                    </li>
                    <li>
                        <span class="ico"><i class="fa-solid fa-envelope" aria-hidden="true"></i></span>
                        <div><small>Email</small><a href="mailto:{{ config('custom.office_email') }}">{{ config('custom.office_email') }}</a></div>
                    </li>
                </ul>

                <div class="owner-box">
                    <img src="{{ asset('images/avatar.png') }}" alt="{{ config('custom.owner_name') }}" width="60" height="60" loading="lazy">
                    <div>
                        <strong>{{ config('custom.owner_designation') }}: Mr. {{ config('custom.owner_name') }}</strong>
                        <span><a href="mailto:{{ config('custom.owner_email') }}">{{ config('custom.owner_email') }}</a></span>
                        <span><a href="tel:{{ config('custom.owner_phone_tel') }}">{{ config('custom.owner_phone') }}</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
