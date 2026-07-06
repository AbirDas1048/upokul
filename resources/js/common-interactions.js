(function () {
    'use strict';

    const nav = document.getElementById('mainNav');

    function initNav() {
        if (!nav) return;

        const onScroll = () => {
            const y = globalThis.scrollY || globalThis.pageYOffset;
            nav.classList.toggle('scrolled', y > 80);
            document.body.classList.toggle('at-top', y <= 50);
        };

        globalThis.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    function initDrawer() {
        const ham = document.getElementById('ham');
        const drawer = document.getElementById('drawer');
        if (!ham || !drawer) return;

        const closeDrawer = () => {
            ham.classList.remove('open');
            drawer.classList.remove('open');
            ham.setAttribute('aria-expanded', 'false');
        };

        ham.addEventListener('click', () => {
            ham.classList.toggle('open');
            drawer.classList.toggle('open');
            ham.setAttribute('aria-expanded', drawer.classList.contains('open') ? 'true' : 'false');
        });

        document.querySelectorAll('.drawer-link').forEach((link) => {
            link.addEventListener('click', closeDrawer);
        });

        document.addEventListener('click', (e) => {
            if (!drawer.contains(e.target) && !ham.contains(e.target)) {
                closeDrawer();
            }
        });

        globalThis.__closeNavDrawer = closeDrawer;
    }

    function initSmoothScroll() {
        const clamp = (n, min, max) => Math.min(Math.max(n, min), max);
        const filmicEase = (t) => {
            const s = t * t * (3 - 2 * t);
            return s + (Math.sin(s * Math.PI) * 0.015);
        };

        let scrollRaf = null;
        let scrollAbort = false;

        const cancelActiveScroll = () => {
            if (scrollRaf) {
                cancelAnimationFrame(scrollRaf);
                scrollRaf = null;
            }
            scrollAbort = true;
        };

        const getScrollDuration = (distance) => clamp(520 + Math.abs(distance) * 0.35, 520, 1400);

        const smoothScrollTo = (targetY, duration = getScrollDuration(targetY - globalThis.pageYOffset)) => {
            cancelActiveScroll();
            scrollAbort = false;

            const startY = globalThis.pageYOffset;
            const diff = targetY - startY;
            if (Math.abs(diff) < 2) return;

            let startTime = null;
            const stopOnUserIntent = () => {
                scrollAbort = true;
            };

            globalThis.addEventListener('wheel', stopOnUserIntent, { passive: true, once: true });
            globalThis.addEventListener('touchstart', stopOnUserIntent, { passive: true, once: true });
            globalThis.addEventListener('keydown', stopOnUserIntent, { once: true });

            const step = (timestamp) => {
                if (scrollAbort) {
                    scrollRaf = null;
                    return;
                }

                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const eased = filmicEase(progress);
                globalThis.scrollTo(0, startY + diff * eased);

                if (progress < 1) {
                    scrollRaf = requestAnimationFrame(step);
                } else {
                    scrollRaf = null;
                }
            };

            scrollRaf = requestAnimationFrame(step);
        };

        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener('click', (e) => {
                const id = anchor.hash;
                if (!id || id === '#') return;

                const target = document.querySelector(id);
                if (!target) return;

                e.preventDefault();

                if (typeof globalThis.__closeNavDrawer === 'function') {
                    globalThis.__closeNavDrawer();
                }
                if (typeof globalThis.__closeLegacyNavMenu === 'function') {
                    globalThis.__closeLegacyNavMenu();
                }

                const navHeight = nav ? nav.offsetHeight : 90;
                const targetY = target.getBoundingClientRect().top + globalThis.pageYOffset - navHeight;

                if (globalThis.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    cancelActiveScroll();
                    globalThis.scrollTo(0, targetY);
                    return;
                }

                smoothScrollTo(targetY);
            });
        });
    }

    function initReveals() {
        const allReveal = document.querySelectorAll('[data-reveal]');
        if (!allReveal.length) return;

        allReveal.forEach((el) => {
            if (!el.classList.contains('visible')) {
                if (!el.style.opacity) el.style.opacity = '0';
                if (!el.style.transform) el.style.transform = 'translateY(22px)';
                if (!el.style.transition) el.style.transition = 'opacity .7s ease, transform .7s ease';
            }

            const delay = Number(el.dataset.revealDelay || '0');
            if (delay > 0 && !el.style.transitionDelay) {
                el.style.transitionDelay = `${delay}ms`;
            }
        });

        const revObs = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    revObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px 0px 0px' });

        allReveal.forEach((el) => revObs.observe(el));

        let resizeTimer;
        let resizeRaf = null;
        let revealForced = false;

        const forceRevealAll = () => {
            allReveal.forEach((el) => {
                el.classList.add('visible');
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            });
        };

        const endResizeState = () => {
            document.body.classList.remove('is-resizing');
            revealForced = false;
        };

        const handleResizeFrame = () => {
            document.body.classList.add('is-resizing');

            if (!revealForced) {
                forceRevealAll();
                revealForced = true;
            }

            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(endResizeState, 220);

            resizeRaf = null;
        };

        globalThis.addEventListener('resize', () => {
            if (resizeRaf) return;
            resizeRaf = requestAnimationFrame(handleResizeFrame);
        }, { passive: true });
    }

    function animateCounter(el, target) {
        let current = 0;
        const increment = Math.max(1, Math.ceil(target / 80));

        const interval = setInterval(() => {
            current += increment;
            if (current >= target) {
                el.textContent = target;
                clearInterval(interval);
            } else {
                el.textContent = current;
            }
        }, 25);
    }

    function animateAllCounters() {
        document.querySelectorAll('.counter').forEach((counter) => {
            animateCounter(counter, Number(counter.dataset.count));
        });
    }

    function initCounter() {
        const trustedSection = document.querySelector('#trusted');
        if (!trustedSection) return;

        let counterStarted = false;

        const handleIntersection = (entry, observer) => {
            if (!entry.isIntersecting || counterStarted) return;

            counterStarted = true;
            animateAllCounters();
            observer.unobserve(entry.target);
        };

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => handleIntersection(entry, obs));
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -80px 0px'
        });

        observer.observe(trustedSection);
    }

    function initCardSwiper(prefersReducedMotion) {
        if (!document.querySelector('.cardSwiper')) return null;

        return new globalThis.Swiper('.cardSwiper', {
            slidesPerView: 1,
            spaceBetween: 24,
            autoplay: prefersReducedMotion ? false : { delay: 3500, disableOnInteraction: false },
            pagination: { el: '.cardSwiper .swiper-pagination', clickable: true },
            watchOverflow: true,
            breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
        });
    }

    function initFullscreenSwiper() {
        const fullscreenModal = document.querySelector('.fullscreen-modal');
        const fullscreenSelector = '.fullscreenSwiper';

        if (!fullscreenModal || !document.querySelector(fullscreenSelector)) return null;

        const closeModalBtn = fullscreenModal.querySelector('.close-modal');
        const fullscreenSwiper = new globalThis.Swiper(fullscreenSelector, {
            navigation: {
                nextEl: '.fullscreen-modal .swiper-button-next',
                prevEl: '.fullscreen-modal .swiper-button-prev'
            },
            loop: false,
            watchOverflow: true
        });

        let lastGalleryTrigger = null;

        const closeFullscreenModal = () => {
            fullscreenModal.classList.remove('active');
            lastGalleryTrigger?.focus();
        };

        const openFullscreenModal = (card) => {
            lastGalleryTrigger = card;
            fullscreenModal.classList.add('active');
            fullscreenSwiper.slideTo(Number.parseInt(card.dataset.index || '0', 10), 0);
        };

        document.querySelectorAll('.gallery-card').forEach((card) => {
            card.addEventListener('click', () => openFullscreenModal(card));
        });

        closeModalBtn?.addEventListener('click', closeFullscreenModal);
        fullscreenModal.addEventListener('click', (e) => {
            if (e.target === fullscreenModal) closeFullscreenModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && fullscreenModal.classList.contains('active')) {
                closeFullscreenModal();
            }
        });

        return fullscreenSwiper;
    }

    function initReviewSwiper(prefersReducedMotion) {
        if (!document.querySelector('.reviewSwiper')) return null;

        return new globalThis.Swiper('.reviewSwiper', {
            slidesPerView: 1,
            spaceBetween: 24,
            autoplay: prefersReducedMotion ? false : { delay: 4000, disableOnInteraction: false },
            pagination: { el: '.reviewSwiper .swiper-pagination', clickable: true },
            watchOverflow: true,
            breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
        });
    }

    function initSwipers() {
        if (!globalThis.Swiper) return;

        const prefersReducedMotion = globalThis.matchMedia('(prefers-reduced-motion: reduce)').matches;

        initCardSwiper(prefersReducedMotion);
        initFullscreenSwiper();
        initReviewSwiper(prefersReducedMotion);
    }

    function showErrorSwal(errors, fallbackMessage) {
        let msg = '';

        if (errors && typeof errors === 'object') {
            Object.entries(errors).forEach(([, value]) => {
                const message = Array.isArray(value) ? value[0] : value;
                msg += `${message}<br>`;
            });
        } else if (fallbackMessage) {
            msg = fallbackMessage;
        } else {
            msg = 'Something went wrong. Please try again.';
        }

        if (globalThis.Swal) {
            globalThis.Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                html: msg,
                confirmButtonColor: '#ef4444',
                customClass: { htmlContainer: 'swal-html-red-bg' }
            });
        }
    }

    function setSubmitBtnState(submitBtn, isSending) {
        if (!submitBtn) return;

        submitBtn.disabled = isSending;
        submitBtn.innerHTML = isSending
            ? '<i class="fas fa-spinner fa-spin"></i> Sending...'
            : '<i class="fas fa-paper-plane"></i> Send Message';
    }

    function showSendingSwal() {
        globalThis.Swal.fire({
            title: 'Sending Message...',
            text: 'Please wait a moment',
            allowOutsideClick: false,
            didOpen: () => globalThis.Swal.showLoading()
        });
    }

    async function postContactForm(contactForm) {
        const response = await fetch(contactForm.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': contactForm.querySelector('input[name="_token"]')?.value || '',
                'Accept': 'application/json'
            },
            body: new FormData(contactForm)
        });
        const data = await response.json().catch(() => null);

        if (!response.ok) {
            const error = new Error('Contact form submission failed.');
            error.status = response.status;
            error.data = data;
            throw error;
        }

        return data;
    }

    async function submitContactForm(contactForm, submitBtn) {
        setSubmitBtnState(submitBtn, true);
        showSendingSwal();

        try {
            const res = await postContactForm(contactForm);

            if (res?.success) {
                globalThis.Swal.fire({
                    icon: 'success',
                    title: 'Message Sent Successfully!',
                    text: res.message,
                    confirmButtonColor: '#4f46e5'
                });
                contactForm.reset();
            } else {
                showErrorSwal(res?.data, res?.message);
            }
        } catch (error) {
            showErrorSwal(error.data, 'Server error. Please try again later.');
        } finally {
            setSubmitBtnState(submitBtn, false);
        }
    }

    async function confirmContactSubmission() {
        const result = await globalThis.Swal.fire({
            title: 'Send Message?',
            text: 'Are you sure you want to submit this form?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Send',
            confirmButtonColor: '#4f46e5'
        });

        return result.isConfirmed;
    }

    function initContactForm() {
        const contactForm = document.getElementById('contactForm');
        if (!contactForm || !globalThis.Swal) return;

        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (await confirmContactSubmission()) {
                await submitContactForm(contactForm, document.getElementById('submitBtn'));
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initNav();
        initDrawer();
        initSmoothScroll();
        initReveals();
        initCounter();
        initSwipers();
        initContactForm();
    });
})();
