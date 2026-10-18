document.addEventListener('alpine:init', () => {
    Alpine.data('alpConsultation', (items = [], interval = 3000) => ({
        items: [],
        interval: interval,

        currentIndex: 0,

        timer: null,
        startTimer: null,
        observer: null,

        isAnimating: false,
        hasStarted: false,
        fromRight: true,

        animationClasses: [
            'magictime',
            'spaceOutLeft',
            'spaceOutRight',
            'tinLeftIn',
            'tinRightIn',
        ],

        init() {
            this.items = Array.isArray(items)
                ? items.filter((item) => item?.text)
                : [];

            this.currentIndex = 0;
            this.fromRight = true;

            if (this.items.length <= 1) {
                return;
            }

            this.observeBlock();
        },

        get currentText() {
            return this.items[this.currentIndex]?.text ?? '';
        },

        get noteElements() {
            return [
                this.$refs.noteDesktop,
                this.$refs.noteResponsive,
                this.$refs.noteMobile,
            ].filter(Boolean);
        },

        observeBlock() {
            if (!this.$refs.root || !('IntersectionObserver' in window)) {
                this.startAfterAos();
                return;
            }

            this.observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting || this.hasStarted) {
                            return;
                        }

                        this.hasStarted = true;

                        this.startAfterAos();

                        this.observer.disconnect();
                        this.observer = null;
                    });
                },
                {
                    threshold: 0.2,
                }
            );

            this.observer.observe(this.$refs.root);
        },

        startAfterAos() {
            this.startTimer = window.setTimeout(() => {
                this.start();
            }, 1000);
        },

        start() {
            this.stop();

            if (this.items.length <= 1) {
                return;
            }

            this.timer = window.setInterval(() => {
                this.next();
            }, this.interval);
        },

        stop() {
            if (!this.timer) {
                return;
            }

            window.clearInterval(this.timer);

            this.timer = null;
        },

        next() {
            const notes = this.noteElements;

            if (
                this.isAnimating ||
                this.items.length <= 1 ||
                !notes.length
            ) {
                return;
            }

            this.isAnimating = true;

            const outAnimation = this.fromRight
                ? 'spaceOutLeft'
                : 'spaceOutRight';

            const inAnimation = this.fromRight
                ? 'tinRightIn'
                : 'tinLeftIn';

            notes.forEach((note) => {
                this.playAnimation(
                    note,
                    outAnimation,
                    380
                );
            });

            window.setTimeout(() => {
                this.currentIndex =
                    (this.currentIndex + 1) %
                    this.items.length;

                this.$nextTick(() => {
                    this.noteElements.forEach((note) => {
                        this.playAnimation(
                            note,
                            inAnimation,
                            520
                        );
                    });

                    this.fromRight =
                        !this.fromRight;

                    window.setTimeout(() => {
                        this.isAnimating = false;
                    }, 540);
                });
            }, 340);
        },

        playAnimation(element, animation, duration = 520) {
            if (!element) {
                return;
            }

            this.animationClasses.forEach((className) => {
                element.classList.remove(className);
            });

            void element.offsetWidth;

            element.style.animationDuration =
                `${duration}ms`;

            element.classList.add(
                'magictime',
                animation
            );
        },

        destroy() {
            this.stop();

            if (this.startTimer) {
                window.clearTimeout(
                    this.startTimer
                );
            }

            if (this.observer) {
                this.observer.disconnect();
            }
        },
    }));
});