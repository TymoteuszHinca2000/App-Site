document.addEventListener('alpine:init', () => {
    Alpine.data('alpHomeHero', (items = [], interval = 6500) => ({
        items: Array.isArray(items) ? items : [],
        interval,
        activeIndex: 0,
        timer: null,
        motionTimer: null,
        motionFrame1: null,
        motionFrame2: null,
        motionActive: false,
        motionDirection: true,
        prefersReducedMotion: false,
        waveCanvas: null,
        waveContext: null,
        waveWidth: 0,
        waveHeight: 0,
        waveColor: [16, 216, 181],
        waveFrame: null,
        waveAnimating: false,
        waveVisible: false,
        waveObserver: null,
        waveResizeObserver: null,
        waveMediaQuery: null,
        waveMotionChange: null,
        waveVisibilityChange: null,
        waveWindowResize: null,

        init() {
            this.waveMediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            this.prefersReducedMotion = this.waveMediaQuery.matches;
            this.$nextTick(() => this.initWaves());
        },

        scrollBelowHero() {
            const bottom = this.$root.getBoundingClientRect().bottom + window.scrollY;

            window.scrollTo({
                top: bottom,
                behavior: this.prefersReducedMotion ? 'auto' : 'smooth',
            });
        },

        startSlider() {
            if (
                this.timer ||
                this.items.length <= 1 ||
                document.hidden ||
                !this.waveVisible
            ) {
                return;
            }

            this.timer = window.setInterval(() => {
                this.next();
            }, this.interval);
        },

        stopSlider() {
            if (this.timer !== null) {
                window.clearInterval(this.timer);
                this.timer = null;
            }
        },

        next() {
            if (this.items.length <= 1) {
                return;
            }

            this.stopMotion();
            this.activeIndex = (this.activeIndex + 1) % this.items.length;
            this.motionDirection = !this.motionDirection;
            this.startMotion();
        },

        startMotion() {
            if (
                !this.items.length ||
                this.prefersReducedMotion ||
                document.hidden ||
                !this.waveVisible ||
                this.motionFrame1 !== null ||
                this.motionFrame2 !== null ||
                this.motionTimer !== null
            ) {
                return;
            }

            this.motionFrame1 = window.requestAnimationFrame(() => {
                this.motionFrame1 = null;
                this.motionFrame2 = window.requestAnimationFrame(() => {
                    this.motionFrame2 = null;
                    this.motionActive = true;
                    this.scheduleMotion();
                });
            });
        },

        scheduleMotion() {
            if (
                this.prefersReducedMotion ||
                document.hidden ||
                !this.waveVisible
            ) {
                return;
            }

            this.motionTimer = window.setTimeout(() => {
                this.motionTimer = null;
                this.motionActive = !this.motionActive;

                if (!this.motionActive) {
                    this.motionDirection = !this.motionDirection;
                }

                this.scheduleMotion();
            }, 7600);
        },

        stopMotion() {
            if (this.motionTimer !== null) {
                window.clearTimeout(this.motionTimer);
                this.motionTimer = null;
            }

            if (this.motionFrame1 !== null) {
                window.cancelAnimationFrame(this.motionFrame1);
                this.motionFrame1 = null;
            }

            if (this.motionFrame2 !== null) {
                window.cancelAnimationFrame(this.motionFrame2);
                this.motionFrame2 = null;
            }

            this.motionActive = false;
        },

        initWaves() {
            const canvas = this.$refs.waves;

            if (!canvas) {
                return;
            }

            const context = canvas.getContext('2d', { alpha: true });

            if (!context) {
                return;
            }

            this.waveCanvas = canvas;
            this.waveContext = context;
            this.waveMotionChange = () => {
                this.prefersReducedMotion = this.waveMediaQuery.matches;
                this.updatePlaybackState();
            };
            this.waveVisibilityChange = () => this.updatePlaybackState();
            this.waveWindowResize = () => this.resizeWaves();

            const brand = getComputedStyle(this.$root)
                .getPropertyValue('--color-brand')
                .trim();
            const color = this.parseColor(brand);

            if (color) {
                this.waveColor = color;
            }

            if (this.waveMediaQuery.addEventListener) {
                this.waveMediaQuery.addEventListener('change', this.waveMotionChange);
            } else {
                this.waveMediaQuery.addListener(this.waveMotionChange);
            }

            document.addEventListener('visibilitychange', this.waveVisibilityChange);

            if ('ResizeObserver' in window) {
                this.waveResizeObserver = new ResizeObserver(() => this.resizeWaves());
                this.waveResizeObserver.observe(this.$root);
            } else {
                window.addEventListener('resize', this.waveWindowResize);
            }

            if ('IntersectionObserver' in window) {
                this.waveObserver = new IntersectionObserver(([entry]) => {
                    this.waveVisible = !!entry?.isIntersecting;
                    this.updatePlaybackState();
                }, {
                    threshold: 0.03,
                    rootMargin: '100px 0px',
                });
                this.waveObserver.observe(this.$root);
            } else {
                this.waveVisible = true;
            }

            this.resizeWaves();
            this.updatePlaybackState();
        },

        parseColor(value) {
            const raw = String(value || '').trim().replace(/^#/, '');

            if (/^[0-9a-f]{3}$/i.test(raw)) {
                return raw.split('').map((part) => parseInt(part + part, 16));
            }

            if (/^[0-9a-f]{6}$/i.test(raw)) {
                return [0, 2, 4].map((offset) => parseInt(raw.slice(offset, offset + 2), 16));
            }

            const rgb = String(value || '').match(/rgba?\(\s*(\d+)[,\s]+(\d+)[,\s]+(\d+)/i);

            return rgb ? rgb.slice(1, 4).map(Number) : null;
        },

        resizeWaves() {
            if (!this.waveCanvas || !this.waveContext) {
                return;
            }

            const rect = this.waveCanvas.getBoundingClientRect();
            const width = Math.round(rect.width);
            const height = Math.round(rect.height);

            if (!width || !height) {
                return;
            }

            if (this.waveWidth === width && this.waveHeight === height) {
                return;
            }

            this.waveWidth = width;
            this.waveHeight = height;

            const ratio = Math.min(window.devicePixelRatio || 1, 2);
            this.waveCanvas.width = Math.round(width * ratio);
            this.waveCanvas.height = Math.round(height * ratio);
            this.waveContext.setTransform(ratio, 0, 0, ratio, 0, 0);
            this.drawWaves(0);
        },

        drawWaves(time) {
            const ctx = this.waveContext;
            const width = this.waveWidth;
            const height = this.waveHeight;

            if (!ctx || !width || !height) {
                return;
            }

            ctx.clearRect(0, 0, width, height);

            const mobile = width < 768;
            const strands = mobile ? 26 : 42;
            const strandGap = mobile ? 1.35 : 1.7;
            const amplitude = Math.min(height * (mobile ? 0.052 : 0.10), mobile ? 30 : 88);
            const baseY = height * (mobile ? 0.69 : 0.66);
            const rgb = this.waveColor.join(',');
            const phase = time * 0.00027;
            const middle = (strands - 1) / 2;
            const gradient = ctx.createLinearGradient(0, 0, width, 0);

            gradient.addColorStop(0, `rgba(${rgb},0.05)`);
            gradient.addColorStop(0.25, `rgba(${rgb},0.19)`);
            gradient.addColorStop(0.57, `rgba(${rgb},0.27)`);
            gradient.addColorStop(0.82, `rgba(${rgb},0.14)`);
            gradient.addColorStop(1, `rgba(${rgb},0.05)`);

            ctx.lineWidth = mobile ? 0.7 : 0.85;
            ctx.lineCap = 'round';
            ctx.strokeStyle = gradient;

            for (let strand = 0; strand < strands; strand += 1) {
                const distance = strand - middle;
                const strandPhase = distance * 0.018;
                const yOffset = distance * strandGap;

                ctx.beginPath();

                for (let x = 0; x <= width + 7; x += 7) {
                    const nx = x / width;
                    const main = Math.sin(nx * Math.PI * 3.1 - phase + strandPhase) * amplitude;
                    const secondary = Math.sin(nx * Math.PI * 5.4 + phase * 0.7 + strandPhase) * amplitude * 0.18;
                    const slowDrift = Math.sin(phase * 0.65 + nx * 2.7) * amplitude * 0.18;
                    const y = baseY + yOffset + main + secondary + slowDrift;

                    if (x === 0) {
                        ctx.moveTo(x, y);
                    } else {
                        ctx.lineTo(x, y);
                    }
                }

                ctx.stroke();
            }
        },

        startWaves() {
            if (this.waveAnimating || !this.waveContext) {
                return;
            }

            this.waveAnimating = true;
            let lastFrameTime = 0;

            const tick = (time) => {
                if (!this.waveAnimating) {
                    return;
                }

                if (time - lastFrameTime >= 30) {
                    this.drawWaves(time);
                    lastFrameTime = time;
                }

                this.waveFrame = window.requestAnimationFrame(tick);
            };

            this.waveFrame = window.requestAnimationFrame(tick);
        },

        stopWaves() {
            this.waveAnimating = false;

            if (this.waveFrame !== null) {
                window.cancelAnimationFrame(this.waveFrame);
                this.waveFrame = null;
            }
        },

        updatePlaybackState() {
            if (!this.waveContext) {
                return;
            }

            const canPlay = this.waveVisible && !document.hidden;

            if (!canPlay) {
                this.stopSlider();
                this.stopMotion();
                this.stopWaves();
                return;
            }

            this.startSlider();

            if (this.prefersReducedMotion) {
                this.stopMotion();
                this.stopWaves();
                this.drawWaves(0);
            } else {
                this.startMotion();
                this.startWaves();
            }
        },

        destroy() {
            this.stopSlider();
            this.stopMotion();
            this.stopWaves();
            this.waveObserver?.disconnect();
            this.waveResizeObserver?.disconnect();

            if (this.waveMediaQuery && this.waveMotionChange) {
                if (this.waveMediaQuery.removeEventListener) {
                    this.waveMediaQuery.removeEventListener('change', this.waveMotionChange);
                } else {
                    this.waveMediaQuery.removeListener(this.waveMotionChange);
                }
            }

            if (this.waveVisibilityChange) {
                document.removeEventListener('visibilitychange', this.waveVisibilityChange);
            }

            if (this.waveWindowResize) {
                window.removeEventListener('resize', this.waveWindowResize);
            }
        },
    }));
});
