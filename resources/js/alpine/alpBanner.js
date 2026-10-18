document.addEventListener('alpine:init', () => {
    Alpine.data('alpBanner', (items = [], interval = 6500) => ({
        items: Array.isArray(items)
            ? items
            : [],

        interval,

        activeIndex: 0,

        timer: null,
        motionTimer: null,
        observer: null,
        visibilityHandler: null,

        inView: false,

        motionActive: false,
        motionDirection: true,

        prefersReducedMotion: false,

        init() {
            if (!this.items.length) {
                return;
            }

            this.prefersReducedMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches;

            this.visibilityHandler = () => {
                if (document.hidden) {
                    this.pause();

                    return;
                }

                if (this.inView) {
                    this.resume();
                }
            };

            document.addEventListener(
                'visibilitychange',
                this.visibilityHandler
            );

            if (!('IntersectionObserver' in window)) {
                this.inView = true;

                this.resume();

                return;
            }

            this.observer = new IntersectionObserver(
                (entries) => {
                    this.inView =
                        entries[0]?.isIntersecting ?? false;

                    if (this.inView) {
                        this.resume();
                    } else {
                        this.pause();
                    }
                },
                {
                    threshold: 0.05,
                    rootMargin: '100px 0px',
                }
            );

            this.observer.observe(this.$el);
        },

        resume() {
            if (!this.items.length) {
                return;
            }

            this.startMotion();

            if (this.items.length > 1) {
                this.startSlider();
            }
        },

        pause() {
            this.stopSlider();
            this.stopMotion();
        },

        startSlider() {
            if (
                this.timer ||
                this.items.length <= 1 ||
                document.hidden ||
                !this.inView
            ) {
                return;
            }

            this.timer = window.setInterval(() => {
                this.next();
            }, this.interval);
        },

        stopSlider() {
            if (!this.timer) {
                return;
            }

            window.clearInterval(
                this.timer
            );

            this.timer = null;
        },

        next() {
            if (this.items.length <= 1) {
                return;
            }

            this.motionActive = false;

            this.activeIndex =
                (this.activeIndex + 1) %
                this.items.length;

            this.motionDirection =
                !this.motionDirection;

            this.restartMotion();
        },

        startMotion() {
            if (
                this.prefersReducedMotion ||
                document.hidden ||
                !this.inView
            ) {
                return;
            }

            if (this.motionTimer) {
                return;
            }

            this.$nextTick(() => {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        this.motionActive = true;

                        this.scheduleMotion();
                    });
                });
            });
        },

        restartMotion() {
            this.stopMotion();

            if (
                this.prefersReducedMotion ||
                document.hidden ||
                !this.inView
            ) {
                return;
            }

            this.$nextTick(() => {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        this.motionActive = true;

                        this.scheduleMotion();
                    });
                });
            });
        },

        scheduleMotion() {
            if (
                this.prefersReducedMotion ||
                document.hidden ||
                !this.inView
            ) {
                return;
            }

            this.motionTimer = window.setTimeout(() => {
                this.motionActive =
                    !this.motionActive;

                if (!this.motionActive) {
                    this.motionDirection =
                        !this.motionDirection;
                }

                this.motionTimer = null;

                this.scheduleMotion();
            }, 7600);
        },

        stopMotion() {
            if (this.motionTimer) {
                window.clearTimeout(
                    this.motionTimer
                );

                this.motionTimer = null;
            }

            this.motionActive = false;
        },

        destroy() {
            this.pause();

            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }

            if (this.visibilityHandler) {
                document.removeEventListener(
                    'visibilitychange',
                    this.visibilityHandler
                );

                this.visibilityHandler = null;
            }
        },
    }));
});