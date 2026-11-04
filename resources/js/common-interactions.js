(function () {
    'use strict';

    const reducedMotion = () => globalThis.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const nav = document.getElementById('mainNav');

    /* ---------------------------------------------------------------
       Navbar: transparent over the hero, solid once scrolled.
       Scroll-spy highlights the link of the section in view.
    --------------------------------------------------------------- */
    function initNav() {
        if (!nav) return;

        const onScroll = () => {
            nav.classList.toggle('scrolled', (globalThis.scrollY || 0) > 40);
        };
        globalThis.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        const links = [...nav.querySelectorAll('.nav-links a[href^="#"]')];
        const map = new Map();
        links.forEach((a) => {
            const section = document.querySelector(a.hash);
            if (section) map.set(section, a);
        });
        if (!map.size) return;

        const spy = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                const link = map.get(entry.target);
                if (!link) return;
                if (entry.isIntersecting) {
                    links.forEach((l) => l.classList.remove('active'));
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }, { rootMargin: '-45% 0px -50% 0px', threshold: 0 });
        map.forEach((_, section) => spy.observe(section));
    }

    /* ---------------------------------------------------------------
       Mobile drawer (closes on link click, outside click, Escape)
    --------------------------------------------------------------- */
    function initDrawer() {
        const ham = document.getElementById('ham');
        const drawer = document.getElementById('drawer');
        if (!ham || !drawer || !nav) return;

        const setOpen = (open) => {
            drawer.classList.toggle('open', open);
            nav.classList.toggle('menu-open', open);
            ham.setAttribute('aria-expanded', String(open));
            ham.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            ham.firstElementChild?.classList.toggle('fa-bars', !open);
            ham.firstElementChild?.classList.toggle('fa-xmark', open);
        };

        ham.addEventListener('click', () => setOpen(!drawer.classList.contains('open')));
        drawer.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => setOpen(false)));
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target)) setOpen(false);
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && drawer.classList.contains('open')) {
                setOpen(false);
                ham.focus();
            }
        });
        globalThis.matchMedia('(min-width: 75rem)').addEventListener('change', (e) => {
            if (e.matches) setOpen(false);
        });

        globalThis.__closeNavDrawer = () => setOpen(false);
    }

    /* ---------------------------------------------------------------
       Smooth in-page anchor scrolling (respects reduced motion)
    --------------------------------------------------------------- */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener('click', (e) => {
                const id = anchor.hash;
                if (!id || id === '#') return;

                const target = document.querySelector(id);
                if (!target) return;

                e.preventDefault();
                globalThis.__closeNavDrawer?.();

                const navHeight = nav ? nav.offsetHeight : 72;
                const top = id === '#hero'
                    ? 0
                    : target.getBoundingClientRect().top + globalThis.pageYOffset - navHeight + 8;

                globalThis.scrollTo({ top, behavior: reducedMotion() ? 'auto' : 'smooth' });

                // Keep the address bar clean: drop any #hash instead of adding one.
                if (location.hash && history.replaceState) {
                    history.replaceState(null, '', location.pathname + location.search);
                }
                if (!target.hasAttribute('tabindex')) target.setAttribute('tabindex', '-1');
                target.focus({ preventScroll: true });
            });
        });
    }

    /* ---------------------------------------------------------------
       Scroll reveal (CSS hides [data-reveal] only when the .js class is set)
    --------------------------------------------------------------- */
    function initReveals() {
        const items = document.querySelectorAll('[data-reveal]');
        if (!items.length) return;

        if (reducedMotion() || !('IntersectionObserver' in globalThis)) {
            items.forEach((el) => el.classList.add('is-in'));
            return;
        }

        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-in');
                io.unobserve(entry.target);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

        items.forEach((el) => io.observe(el));
    }

    /* ---------------------------------------------------------------
       Counters (start when the section is seen, no flash of "0")
    --------------------------------------------------------------- */
    function initCounter() {
        const counters = document.querySelectorAll('.counter');
        const section = document.querySelector('#trusted');
        if (!counters.length || !section) return;

        const run = () => {
            counters.forEach((el) => {
                const target = Number(el.dataset.count) || 0;
                if (reducedMotion()) {
                    el.textContent = target;
                    return;
                }
                const duration = 1600;
                const start = performance.now();
                const tick = (now) => {
                    const p = Math.min((now - start) / duration, 1);
                    el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
                    if (p < 1) requestAnimationFrame(tick);
                };
                el.textContent = '0';
                requestAnimationFrame(tick);
            });
        };

        const io = new IntersectionObserver((entries, obs) => {
            if (entries.some((e) => e.isIntersecting)) {
                run();
                obs.disconnect();
            }
        }, { threshold: 0.25 });
        io.observe(section);
    }

    /* ---------------------------------------------------------------
       Swipers: gallery cards, fullscreen viewer, reviews
    --------------------------------------------------------------- */
    function initCardSwiper(autoplay) {
        if (!document.querySelector('.cardSwiper')) return;

        new globalThis.Swiper('.cardSwiper', {
            slidesPerView: 1.08,
            spaceBetween: 20,
            grabCursor: true,
            autoplay: autoplay ? { delay: 4200, disableOnInteraction: false, pauseOnMouseEnter: true } : false,
            pagination: { el: '.cardSwiper .swiper-pagination', clickable: true },
            breakpoints: { 640: { slidesPerView: 2, spaceBetween: 24 }, 1024: { slidesPerView: 3, spaceBetween: 28 } }
        });
    }

    function initFullscreenSwiper() {
        const modal = document.querySelector('.fullscreen-modal');
        if (!modal || !document.querySelector('.fullscreenSwiper')) return;

        const closeBtn = modal.querySelector('.close-modal');
        const swiper = new globalThis.Swiper('.fullscreenSwiper', {
            navigation: { nextEl: '.fullscreen-modal .swiper-button-next', prevEl: '.fullscreen-modal .swiper-button-prev' },
            keyboard: { enabled: true },
            watchOverflow: true
        });

        let lastTrigger = null;
        const focusables = () => [...modal.querySelectorAll('button, [href], [tabindex]:not([tabindex="-1"])')]
            .filter((el) => !el.disabled && !el.classList.contains('swiper-button-disabled'));

        const close = () => {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            lastTrigger?.focus();
        };
        const open = (card) => {
            lastTrigger = card;
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            swiper.slideTo(Number.parseInt(card.dataset.index || '0', 10), 0);
            closeBtn?.focus();
        };

        document.querySelectorAll('.gallery-card').forEach((card) => card.addEventListener('click', () => open(card)));
        closeBtn?.addEventListener('click', close);
        modal.addEventListener('click', (e) => { if (e.target === modal) close(); });

        document.addEventListener('keydown', (e) => {
            if (!modal.classList.contains('active')) return;
            if (e.key === 'Escape') return close();
            if (e.key !== 'Tab') return;

            const f = focusables();
            if (!f.length) return e.preventDefault();
            const first = f[0];
            const last = f.at(-1);
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        });
    }

    function initReviewSwiper(autoplay) {
        if (!document.querySelector('.reviewSwiper')) return;

        new globalThis.Swiper('.reviewSwiper', {
            slidesPerView: 1.05,
            spaceBetween: 20,
            grabCursor: true,
            autoplay: autoplay ? { delay: 5200, disableOnInteraction: false, pauseOnMouseEnter: true } : false,
            pagination: { el: '.reviewSwiper .swiper-pagination', clickable: true },
            breakpoints: { 768: { slidesPerView: 2, spaceBetween: 24 }, 1100: { slidesPerView: 3, spaceBetween: 28 } }
        });
    }

    function initSwipers() {
        if (!globalThis.Swiper) return;
        const autoplay = !reducedMotion();
        initCardSwiper(autoplay);
        initFullscreenSwiper();
        initReviewSwiper(autoplay);
    }

    /* ---------------------------------------------------------------
       "Enquire / Plan this trip" links pre-fill the contact subject
    --------------------------------------------------------------- */
    function initEnquiryLinks() {
        const subject = document.getElementById('subject');
        if (!subject) return;
        document.querySelectorAll('[data-enquiry]').forEach((link) => {
            link.addEventListener('click', () => {
                subject.value = link.dataset.enquiry.slice(0, 100);
            });
        });
    }

    /* ---------------------------------------------------------------
       Contact form (AJAX + SweetAlert, brand coloured)
    --------------------------------------------------------------- */
    const BRAND = '#0f6d6f';
    const escapeHtml = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function showErrorSwal(errors, fallbackMessage) {
        let html = '';
        if (errors && typeof errors === 'object') {
            Object.values(errors).forEach((value) => {
                html += `<div>${escapeHtml(Array.isArray(value) ? value[0] : value)}</div>`;
            });
        } else {
            html = escapeHtml(fallbackMessage || 'Something went wrong. Please try again.');
        }

        globalThis.Swal?.fire({
            icon: 'error',
            title: 'Please check your details',
            html: `<div class="swal-error-box">${html}</div>`,
            confirmButtonColor: BRAND
        });
    }

    function setSubmitBtnState(btn, sending) {
        if (!btn) return;
        btn.disabled = sending;
        btn.innerHTML = sending
            ? '<i class="fa-solid fa-spinner fa-spin"></i> Sending...'
            : '<i class="fa-solid fa-paper-plane"></i> Send Message';
    }

    async function postContactForm(form) {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                'Accept': 'application/json'
            },
            body: new FormData(form)
        });
        const data = await response.json().catch(() => null);

        if (!response.ok) {
            const error = new Error('Contact form submission failed.');
            error.data = data?.errors || data?.data || null;
            throw error;
        }
        return data;
    }

    async function submitContactForm(form, btn) {
        setSubmitBtnState(btn, true);
        globalThis.Swal.fire({
            title: 'Sending your message…',
            text: 'Please wait a moment',
            allowOutsideClick: false,
            didOpen: () => globalThis.Swal.showLoading()
        });

        try {
            const res = await postContactForm(form);
            if (res?.success) {
                globalThis.Swal.fire({
                    icon: 'success',
                    title: 'Message sent!',
                    text: res.message || 'Thank you — our team will be in touch shortly.',
                    confirmButtonColor: BRAND
                });
                form.reset();
            } else {
                showErrorSwal(res?.data, res?.message);
            }
        } catch (error) {
            showErrorSwal(error.data, 'Server error. Please try again later.');
        } finally {
            setSubmitBtnState(btn, false);
        }
    }

    function initContactForm() {
        const form = document.getElementById('contactForm');
        if (!form || !globalThis.Swal) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!form.reportValidity()) return;
            await submitContactForm(form, document.getElementById('submitBtn'));
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initNav();
        initDrawer();
        initSmoothScroll();
        initReveals();
        initCounter();
        initSwipers();
        initEnquiryLinks();
        initContactForm();
    });
})();
