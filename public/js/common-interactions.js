(function () {
    'use strict';

    const nav = document.getElementById('mainNav');

    function initNav() {
        if (!nav) return;

        const onScroll = () => {
            const y = window.scrollY || window.pageYOffset;
            // nav.classList.toggle('show', y > 50);
            nav.classList.toggle('scrolled', y > 80);
            document.body.classList.toggle('at-top', y <= 50);
        };

        window.addEventListener('scroll', onScroll, { passive: true });
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

        window.__closeNavDrawer = closeDrawer;
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

        const smoothScrollTo = (targetY, duration = getScrollDuration(targetY - window.pageYOffset)) => {
            cancelActiveScroll();
            scrollAbort = false;

            const startY = window.pageYOffset;
            const diff = targetY - startY;
            if (Math.abs(diff) < 2) return;

            let startTime = null;
            const stopOnUserIntent = () => {
                scrollAbort = true;
            };

            window.addEventListener('wheel', stopOnUserIntent, { passive: true, once: true });
            window.addEventListener('touchstart', stopOnUserIntent, { passive: true, once: true });
            window.addEventListener('keydown', stopOnUserIntent, { once: true });

            const step = (timestamp) => {
                if (scrollAbort) {
                    scrollRaf = null;
                    return;
                }

                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const eased = filmicEase(progress);
                window.scrollTo(0, startY + diff * eased);

                if (progress < 1) {
                    scrollRaf = requestAnimationFrame(step);
                } else {
                    scrollRaf = null;
                }
            };

            scrollRaf = requestAnimationFrame(step);
        };

        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener('click', function (e) {
                const id = this.getAttribute('href');
                if (!id || id === '#') return;

                const target = document.querySelector(id);
                if (!target) return;

                e.preventDefault();

                if (typeof window.__closeNavDrawer === 'function') {
                    window.__closeNavDrawer();
                }
                if (typeof window.__closeLegacyNavMenu === 'function') {
                    window.__closeLegacyNavMenu();
                }

                const navHeight = nav ? nav.offsetHeight : 90;
                const targetY = target.getBoundingClientRect().top + window.pageYOffset - navHeight;

                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    cancelActiveScroll();
                    window.scrollTo(0, targetY);
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

            const delay = Number(el.getAttribute('data-reveal-delay') || '0');
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

        window.addEventListener('resize', () => {
            if (resizeRaf) return;

            resizeRaf = requestAnimationFrame(() => {
                document.body.classList.add('is-resizing');

                if (!revealForced) {
                    allReveal.forEach((el) => {
                        el.classList.add('visible');
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    });
                    revealForced = true;
                }

                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    document.body.classList.remove('is-resizing');
                    revealForced = false;
                }, 220);

                resizeRaf = null;
            });
        }, { passive: true });
    }

    function initCounter() {
        const trustedSection = document.querySelector('#trusted');
        if (!trustedSection) return;

        let counterStarted = false;

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

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && !counterStarted) {
                    counterStarted = true;
                    document.querySelectorAll('.counter').forEach((counter) => {
                        animateCounter(counter, Number(counter.dataset.count));
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -80px 0px'
        });

        observer.observe(trustedSection);
    }

    function initSwipers() {
        if (!window.Swiper) return;

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (document.querySelector('.cardSwiper')) {
            new Swiper('.cardSwiper', {
                slidesPerView: 1,
                spaceBetween: 24,
                autoplay: prefersReducedMotion ? false : { delay: 3500, disableOnInteraction: false },
                pagination: { el: '.cardSwiper .swiper-pagination', clickable: true },
                watchOverflow: true,
                breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
            });
        }

        const fullscreenModal = document.querySelector('.fullscreen-modal');
        const fullscreenSelector = '.fullscreenSwiper';

        if (fullscreenModal && document.querySelector(fullscreenSelector)) {
            const closeModalBtn = fullscreenModal.querySelector('.close-modal');
            const fullscreenSwiper = new Swiper(fullscreenSelector, {
                navigation: {
                    nextEl: '.fullscreen-modal .swiper-button-next',
                    prevEl: '.fullscreen-modal .swiper-button-prev'
                },
                loop: false,
                watchOverflow: true
            });

            let lastGalleryTrigger = null;
            document.querySelectorAll('.gallery-card').forEach((card) => {
                card.addEventListener('click', () => {
                    lastGalleryTrigger = card;
                    fullscreenModal.classList.add('active');
                    fullscreenSwiper.slideTo(parseInt(card.dataset.index || '0', 10), 0);
                });
            });

            const closeFullscreenModal = () => {
                fullscreenModal.classList.remove('active');
                if (lastGalleryTrigger) lastGalleryTrigger.focus();
            };

            closeModalBtn?.addEventListener('click', closeFullscreenModal);
            fullscreenModal.addEventListener('click', (e) => {
                if (e.target === fullscreenModal) closeFullscreenModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && fullscreenModal.classList.contains('active')) {
                    closeFullscreenModal();
                }
            });
        }

        if (document.querySelector('.reviewSwiper')) {
            new Swiper('.reviewSwiper', {
                slidesPerView: 1,
                spaceBetween: 24,
                autoplay: prefersReducedMotion ? false : { delay: 4000, disableOnInteraction: false },
                pagination: { el: '.reviewSwiper .swiper-pagination', clickable: true },
                watchOverflow: true,
                breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
            });
        }
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

        if (window.Swal) {
            window.Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                html: msg,
                confirmButtonColor: '#ef4444',
                customClass: { htmlContainer: 'swal-html-red-bg' }
            });
        }
    }

    function initContactForm() {
        const contactForm = document.getElementById('contactForm');
        if (!contactForm || !window.Swal) return;

        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const setSubmitBtnState = (isSending) => {
                if (!submitBtn) return;
                submitBtn.disabled = isSending;
                submitBtn.innerHTML = isSending
                    ? '<i class="fas fa-spinner fa-spin"></i> Sending...'
                    : '<i class="fas fa-paper-plane"></i> Send Message';
            };

            Swal.fire({
                title: 'Send Message?',
                text: 'Are you sure you want to submit this form?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Send',
                confirmButtonColor: '#4f46e5'
            }).then((result) => {
                if (!result.isConfirmed) return;

                setSubmitBtnState(true);

                Swal.fire({
                    title: 'Sending Message...',
                    text: 'Please wait a moment',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                })
                    .then(async (response) => {
                        const data = await response.json().catch(() => null);
                        if (!response.ok) throw { status: response.status, data };
                        return data;
                    })
                    .then((res) => {
                        if (res && res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Message Sent Successfully!',
                                text: res.message,
                                confirmButtonColor: '#4f46e5'
                            });
                            form.reset();
                        } else {
                            showErrorSwal(res?.data, res?.message);
                        }
                    })
                    .catch(() => {
                        showErrorSwal(null, 'Server error. Please try again later.');
                    })
                    .finally(() => {
                        setSubmitBtnState(false);
                    });
            });
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
