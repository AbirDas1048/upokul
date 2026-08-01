<section id="contact">
    <div class="container">
        <div class="contact-hd">
            <span class="section-badge" data-reveal>Get In Touch</span>
            <h2 class="section-title" data-reveal style="transition-delay:.05s">Contact <span>Us</span></h2>
            <p class="section-subtitle" data-reveal style="transition-delay:.05s">We'd love to hear from you. Get in touch anytime.</p>
        </div>

        <div class="contact-grid">

            <!-- Form -->
            <div class="contact-card" data-reveal>
                <h4>Send a Message</h4>
                <form id="contactForm" method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="visually-hidden">Your Name</label>
                        <input type="text" name="name" id="name" placeholder="Your Name *" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="visually-hidden">Your Email</label>
                        <input type="email" name="email" id="email" placeholder="Your Email *" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="visually-hidden">Phone Number</label>
                        <input type="text" name="phone" id="phone" placeholder="Phone Number *" required>
                    </div>

                    <div class="form-group">
                        <label for="subject" class="visually-hidden">Subject</label>
                        <input type="text" name="subject" id="subject" placeholder="Subject *" required>
                    </div>

                    <div class="form-group">
                        <label for="mail_message" class="visually-hidden">Your Message</label>
                        <textarea rows="4" name="mail_message" id="mail_message" placeholder="Your Message *" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <!-- Map + Info -->
            <div class="contact-card" data-reveal style="transition-delay:.02s">
                <div class="map-box">
                    <iframe
                        title="{{config('app.name')}} Office Location on Google Maps"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3692.600173850267!2d91.81103287529328!3d22.325913979668194!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30acd9df913f946f%3A0x8ac5f87428e5d6cd!2sUpokul%20Travels%20%26%20Holidays!5e0!3m2!1sen!2sbd!4v1768747684228!5m2!1sen!2sbd"
                        loading="lazy"
                        allowfullscreen>
                    </iframe>
                </div>

                <div class="company-info">
                    <h4>{{config('app.name')}}</h4>
                    <ul class="info-list">
                        <li><i class="fas fa-location-dot"></i><span>{{ config('custom.office_address') }}</span></li>
                        <li><i class="fas fa-envelope"></i><span>{{ config('custom.office_email') }}</span></li>
                        <li><i class="fas fa-phone"></i><span>{{ config('custom.office_phone') }}</span></li>
                    </ul>
                </div>

                <div class="owner-box">
                    <img src="{{ asset('images/avatar.png') }}" alt="Owner">
                    <div class="owner-meta">
                        <strong><i class="fas fa-user-tie"></i> {{ config('custom.owner_designation') }}: Mr. {{ config('custom.owner_name') }}</strong>
                        <span><i class="fas fa-envelope"></i> {{ config('custom.owner_email') }}</span>
                        <span><i class="fas fa-phone"></i> {{ config('custom.owner_phone') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
