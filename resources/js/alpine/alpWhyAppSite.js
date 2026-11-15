document.addEventListener('alpine:init', () => {
    Alpine.data('alpWhyAppSite', () => ({
        canvases: [],
        counters: [],
        motionQuery: null,
        sectionObserver: null,
        resizeObserver: null,
        waveFrame: null,
        countFrame: null,
        visible: false,
        started: false,
        completed: false,
        lastWaveFrame: 0,
        color: [16, 216, 181],
        handleVisibility: null,
        handleMotion: null,
        handleResize: null,

        init() {
            this.motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            this.counters = [...this.$root.querySelectorAll('[data-count-target]')].map((element) => ({
                element,
                target: Math.max(0, Number(element.dataset.countTarget) || 0),
            }));

            this.counters.forEach(({ element }) => {
                element.textContent = '0';
            });

            this.color = this.parseColor(getComputedStyle(this.$root).getPropertyValue('--color-brand'));
            this.setupCanvases();

            this.handleVisibility = () => this.updateWaves();
            this.handleMotion = () => {
                if (this.motionQuery.matches) {
                    this.finishCounting();
                }
                this.updateWaves();
            };
            this.handleResize = () => this.resizeCanvases();

            document.addEventListener('visibilitychange', this.handleVisibility);
            this.motionQuery.addEventListener('change', this.handleMotion);

            if ('ResizeObserver' in window) {
                this.resizeObserver = new ResizeObserver(this.handleResize);
                this.canvases.forEach(({ element }) => this.resizeObserver.observe(element));
            } else {
                window.addEventListener('resize', this.handleResize);
            }

            if ('IntersectionObserver' in window) {
                this.sectionObserver = new IntersectionObserver(([entry]) => {
                    this.visible = Boolean(entry?.isIntersecting);
                    if (this.visible && !this.started) {
                        this.startCounting();
                    }
                    this.updateWaves();
                }, { threshold: 0.1, rootMargin: '50px 0px' });
                this.sectionObserver.observe(this.$root);
            } else {
                this.visible = true;
                this.startCounting();
            }

            if (this.motionQuery.matches) {
                this.finishCounting();
            }

            this.resizeCanvases();
            this.updateWaves();
        },

        setupCanvases() {
            [this.$refs.waveTop, this.$refs.waveBottom].forEach((element, index) => {
                const context = element?.getContext('2d', { alpha: true });
                if (context) {
                    this.canvases.push({ element, context, index, width: 0, height: 0 });
                }
            });
        },

        parseColor(value) {
            const hex = String(value || '').trim().replace('#', '');
            if (/^[\da-f]{3}$/i.test(hex)) {
                return [...hex].map((digit) => parseInt(digit + digit, 16));
            }
            if (/^[\da-f]{6}$/i.test(hex)) {
                return [0, 2, 4].map((offset) => parseInt(hex.slice(offset, offset + 2), 16));
            }
            const rgb = String(value || '').match(/rgba?\(\s*(\d+)[,\s]+(\d+)[,\s]+(\d+)/i);
            return rgb ? rgb.slice(1, 4).map(Number) : [16, 216, 181];
        },

        resizeCanvases() {
            const ratio = Math.min(window.devicePixelRatio || 1, 2);
            this.canvases.forEach((state) => {
                const { width, height } = state.element.getBoundingClientRect();
                const nextWidth = Math.round(width);
                const nextHeight = Math.round(height);

                if (!nextWidth || !nextHeight || (state.width === nextWidth && state.height === nextHeight)) {
                    return;
                }

                state.width = nextWidth;
                state.height = nextHeight;
                state.element.width = Math.round(nextWidth * ratio);
                state.element.height = Math.round(nextHeight * ratio);
                state.context.setTransform(ratio, 0, 0, ratio, 0, 0);
                this.drawWave(state, 0);
            });
        },

        drawWave(state, time) {
            const { context, width, height, index } = state;
            if (!width || !height) {
                return;
            }

            context.clearRect(0, 0, width, height);
            const mobile = window.innerWidth < 768;
            const strands = mobile ? 26 : 42;
            const strandGap = mobile ? 1.35 : 1.7;
            const amplitude = Math.min(height * (mobile ? 0.052 : 0.10), mobile ? 30 : 88);
            const baseY = height * (mobile ? 0.69 : 0.66);
            const phase = time * 0.00027 + index * 1.3;
            const middle = (strands - 1) / 2;
            const rgb = this.color.join(',');
            const gradient = context.createLinearGradient(0, 0, width, 0);

            gradient.addColorStop(0, `rgba(${rgb},0.05)`);
            gradient.addColorStop(0.25, `rgba(${rgb},0.19)`);
            gradient.addColorStop(0.57, `rgba(${rgb},0.27)`);
            gradient.addColorStop(0.82, `rgba(${rgb},0.14)`);
            gradient.addColorStop(1, `rgba(${rgb},0.05)`);

            context.lineWidth = mobile ? 0.7 : 0.85;
            context.lineCap = 'round';
            context.strokeStyle = gradient;

            for (let strand = 0; strand < strands; strand += 1) {
                const distance = strand - middle;
                const strandPhase = distance * 0.018;
                const yOffset = distance * strandGap;
                context.beginPath();

                for (let x = 0; x <= width + 7; x += 7) {
                    const nx = x / width;
                    const main = Math.sin(nx * Math.PI * 3.1 - phase + strandPhase) * amplitude;
                    const secondary = Math.sin(nx * Math.PI * 5.4 + phase * 0.7 + strandPhase) * amplitude * 0.18;
                    const slowDrift = Math.sin(phase * 0.65 + nx * 2.7) * amplitude * 0.18;
                    const y = baseY + yOffset + main + secondary + slowDrift;
                    if (x === 0) {
                        context.moveTo(x, y);
                    } else {
                        context.lineTo(x, y);
                    }
                }

                context.stroke();
            }
        },

        updateWaves() {
            const canAnimate = this.visible && !document.hidden && !this.motionQuery?.matches;
            if (!canAnimate) {
                this.stopWaves();
                if (this.motionQuery?.matches) {
                    this.canvases.forEach((state) => this.drawWave(state, 0));
                }
                return;
            }

            if (this.waveFrame !== null || !this.canvases.some(({ width }) => width > 0)) {
                return;
            }

            const tick = (time) => {
                this.waveFrame = null;
                if (!this.visible || document.hidden || this.motionQuery?.matches) {
                    return;
                }
                if (time - this.lastWaveFrame >= 30) {
                    this.canvases.forEach((state) => this.drawWave(state, time));
                    this.lastWaveFrame = time;
                }
                this.waveFrame = requestAnimationFrame(tick);
            };

            this.waveFrame = requestAnimationFrame(tick);
        },

        stopWaves() {
            if (this.waveFrame !== null) {
                cancelAnimationFrame(this.waveFrame);
                this.waveFrame = null;
            }
        },

        startCounting() {
            if (this.started) {
                return;
            }
            this.started = true;
            if (this.motionQuery?.matches || this.counters.length === 0) {
                this.finishCounting();
                return;
            }

            let startTime = null;
            const duration = 1900;
            const tick = (time) => {
                if (startTime === null) {
                    startTime = time;
                }
                const progress = Math.min((time - startTime) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                this.counters.forEach(({ element, target }) => {
                    element.textContent = String(Math.round(target * eased));
                });

                if (progress < 1) {
                    this.countFrame = requestAnimationFrame(tick);
                } else {
                    this.countFrame = null;
                    this.completed = true;
                }
            };

            this.countFrame = requestAnimationFrame(tick);
        },

        finishCounting() {
            if (this.countFrame !== null) {
                cancelAnimationFrame(this.countFrame);
                this.countFrame = null;
            }
            this.counters.forEach(({ element, target }) => {
                element.textContent = String(target);
            });
            this.started = true;
            this.completed = true;
        },

        destroy() {
            this.stopWaves();
            if (this.countFrame !== null) {
                cancelAnimationFrame(this.countFrame);
            }
            this.sectionObserver?.disconnect();
            this.resizeObserver?.disconnect();
            document.removeEventListener('visibilitychange', this.handleVisibility);
            this.motionQuery?.removeEventListener('change', this.handleMotion);
            if (!this.resizeObserver) {
                window.removeEventListener('resize', this.handleResize);
            }
        },
    }));
});
