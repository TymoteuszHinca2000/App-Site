import AOS from 'aos';
import Headroom from 'headroom.js';
import Lenis from 'lenis';

import.meta.glob([
    '../images/**',
    '../fonts/**',
]);

import './alpine/alpSwiper.js';
import './alpine/alpConsultation.js';
import './alpine/alpHomeHero';
import './alpine/alpAboutCompany';
import './alpine/alpHeader.js';
import './alpine/alpBanner.js';
import './alpine/alpSingleFaq.js';
import './alpine/alpRelatedOffers.js';
import './alpine/alpAboutGallery.js';

class App {
    constructor() {
        this.lenis = null;

        this.prefersReducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;

        this.initLenis();
        this.initAos();
        this.initMagicAnimations();
        this.initHeadroom();
        this.handleMenuToggler();
        this.handleMenuScrollLock();
        this.handleAnchors();
        this.refreshAnimationsAfterLoad();
    }


    /* =====================================================
       LENIS
    ===================================================== */

    initLenis() {
        if (this.prefersReducedMotion) {
            return;
        }

        this.lenis = new Lenis({
            autoRaf: true,

            smoothWheel: true,

            syncTouch: false,

            wheelMultiplier: 1,

            touchMultiplier: 1,
        });
    }


    /* =====================================================
       AOS
    ===================================================== */

    initAos() {
        AOS.init({
            duration: 700,

            easing: 'ease-out-cubic',

            offset: 70,

            delay: 0,

            once: true,

            mirror: false,

            anchorPlacement: 'top-bottom',

            disable: this.prefersReducedMotion,
        });
    }


    /* =====================================================
       MAGIC.CSS

       HTML:

       <div data-magic="puffIn">
           ...
       </div>

       delay:

       <div
           data-magic="spaceInDown"
           data-magic-delay="200"
       >
           ...
       </div>

       custom duration:

       <div
           data-magic="swashIn"
           data-magic-duration="1200"
       >
           ...
       </div>
    ===================================================== */

    initMagicAnimations() {
        const elements = document.querySelectorAll('[data-magic]');

        if (!elements.length) {
            return;
        }

        if (this.prefersReducedMotion) {
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    const element = entry.target;

                    const animation = element.dataset.magic;

                    if (!animation) {
                        observer.unobserve(element);
                        return;
                    }

                    const delay = Number(
                        element.dataset.magicDelay || 0
                    );

                    const duration = Number(
                        element.dataset.magicDuration || 0
                    );

                    if (duration > 0) {
                        element.style.animationDuration = `${duration}ms`;
                    }

                    window.setTimeout(() => {
                        element.classList.add(
                            'magictime',
                            ...animation
                                .split(' ')
                                .filter(Boolean)
                        );
                    }, delay);

                    observer.unobserve(element);
                });
            },
            {
                threshold: 0.15,

                rootMargin: '0px 0px -8% 0px',
            }
        );

        elements.forEach((element) => {
            observer.observe(element);
        });
    }


    /* =====================================================
       HEADROOM
    ===================================================== */

    initHeadroom() {
        const header = document.querySelector('.header');

        if (!header) {
            return;
        }

        const headroom = new Headroom(header, {
            offset: 100,

            tolerance: {
                up: 5,
                down: 5,
            },
        });

        headroom.init();
    }


    /* =====================================================
       MOBILE MENU
    ===================================================== */

    handleMenuToggler() {
        const elements = document.querySelectorAll(
            '.js-menu-toggler'
        );

        if (!elements.length) {
            return;
        }

        const setMenuState = (isOpen) => {
            document.body.classList.toggle(
                'menu-open',
                isOpen
            );

            document.documentElement.classList.toggle(
                'menu-open',
                isOpen
            );

            elements.forEach((element) => {
                element.classList.toggle(
                    'is-open',
                    isOpen
                );

                element.setAttribute(
                    'aria-expanded',
                    String(isOpen)
                );
            });

            if (!this.lenis) {
                return;
            }

            if (isOpen) {
                this.lenis.stop();
            } else {
                this.lenis.start();
            }
        };

        elements.forEach((element) => {
            element.addEventListener('click', () => {
                const isOpen =
                    !document.body.classList.contains(
                        'menu-open'
                    );

                setMenuState(isOpen);
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            if (
                !document.body.classList.contains(
                    'menu-open'
                )
            ) {
                return;
            }

            setMenuState(false);
        });
    }

    handleMenuScrollLock() {
        window.addEventListener('app:menu-lock', (event) => {
            const locked = Boolean(event.detail?.locked);

            if (!this.lenis) {
                return;
            }

            if (locked) {
                this.lenis.stop();
                return;
            }

            this.lenis.start();
        });
    }

    /* =====================================================
       ANCHOR LINKS + LENIS
    ===================================================== */

    handleAnchors() {
        if (!this.lenis) {
            return;
        }

        const links = document.querySelectorAll(
            'a[href^="#"]'
        );

        links.forEach((link) => {
            link.addEventListener('click', (event) => {
                const href = link.getAttribute('href');

                if (
                    !href ||
                    href === '#' ||
                    href === '#!'
                ) {
                    return;
                }

                const target = document.querySelector(
                    href
                );

                if (!target) {
                    return;
                }

                event.preventDefault();

                this.lenis.scrollTo(target, {
                    offset: -100,

                    duration: 1.1,
                });

                if (
                    document.body.classList.contains(
                        'menu-open'
                    )
                ) {
                    document.body.classList.remove(
                        'menu-open'
                    );

                    document.documentElement.classList.remove(
                        'menu-open'
                    );

                    document
                        .querySelectorAll(
                            '.js-menu-toggler'
                        )
                        .forEach((element) => {
                            element.classList.remove(
                                'is-open'
                            );

                            element.setAttribute(
                                'aria-expanded',
                                'false'
                            );
                        });

                    this.lenis.start();
                }
            });
        });
    }


    /* =====================================================
       REFRESH AFTER IMAGES / FONTS
    ===================================================== */

    refreshAnimationsAfterLoad() {
        window.addEventListener(
            'load',
            () => {
                window.setTimeout(() => {
                    AOS.refreshHard();
                }, 100);
            },
            {
                once: true,
            }
        );

        if (document.fonts?.ready) {
            document.fonts.ready.then(() => {
                AOS.refreshHard();
            });
        }
    }
}


document.addEventListener('DOMContentLoaded', () => {
    new App();
});