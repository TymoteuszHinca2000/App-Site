import Lenis from 'lenis';

document.addEventListener('alpine:init', () => {
    Alpine.data('alpProjectScreenPreview', () => ({
        lenis: null,
        unsubscribeLenis: null,
        screen: null,
        pageImage: null,
        screenContent: null,
        resizeObserver: null,
        canScroll: false,
        hasInteracted: false,
        isHovering: false,
        isTouchDevice: false,
        isDragging: false,
        progressPercent: 0,
        lastScroll: 0,
        dragStartY: 0,
        dragStartScroll: 0,
        activePointerId: null,
        destroyed: false,
        scrollHandler: null,
        mouseEnterHandler: null,
        mouseLeaveHandler: null,
        pointerDownHandler: null,
        pointerMoveHandler: null,
        pointerUpHandler: null,
        keyHandler: null,
        imageLoadHandler: null,

        init() {
            this.destroyed = false;

            this.$nextTick(() => {
                if (this.destroyed) {
                    return;
                }

                this.screen = this.$refs.screen;
                this.screenContent = this.$refs.screenContent;
                this.pageImage = this.$refs.pageImage;

                if (!this.screen || !this.screenContent || !this.pageImage) {
                    return;
                }

                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                this.isTouchDevice = window.matchMedia('(hover: none)').matches;

                if (!reduceMotion) {
                    this.lenis = new Lenis({
                        wrapper: this.screen,
                        content: this.screenContent,
                        autoRaf: true,
                        smoothWheel: true,
                        syncTouch: false,
                        lerp: 0.085,
                        wheelMultiplier: 1,
                        overscroll: false,
                    });

                    this.unsubscribeLenis = this.lenis.on('scroll', () => {
                        this.handleScroll();
                    });
                }

                this.scrollHandler = () => this.handleScroll();
                this.mouseEnterHandler = () => {
                    this.isHovering = true;
                    this.hasInteracted = false;
                    this.lastScroll = this.screen.scrollTop;
                };
                this.mouseLeaveHandler = () => {
                    this.isHovering = false;
                };
                this.imageLoadHandler = () => this.refreshDimensions();

                this.pointerDownHandler = (event) => {
                    if (event.pointerType !== 'mouse' || event.button !== 0 || this.maxScroll() <= 1) {
                        return;
                    }

                    this.isDragging = true;
                    this.activePointerId = event.pointerId;
                    this.dragStartY = event.clientY;
                    this.dragStartScroll = this.screen.scrollTop;
                    this.screen.setPointerCapture(event.pointerId);
                    event.preventDefault();
                };

                this.pointerMoveHandler = (event) => {
                    if (!this.isDragging || event.pointerId !== this.activePointerId) {
                        return;
                    }

                    const next = Math.max(0, Math.min(this.maxScroll(), this.dragStartScroll + this.dragStartY - event.clientY));
                    this.goTo(next, true);
                };

                this.pointerUpHandler = (event) => {
                    if (event.pointerId !== this.activePointerId) {
                        return;
                    }

                    this.isDragging = false;
                    this.activePointerId = null;

                    if (this.screen.hasPointerCapture(event.pointerId)) {
                        this.screen.releasePointerCapture(event.pointerId);
                    }
                };

                this.keyHandler = (event) => {
                    const keys = ['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', 'Home', 'End', ' '];

                    if (!keys.includes(event.key) || this.maxScroll() <= 1) {
                        return;
                    }

                    const step = this.screen.clientHeight * 0.8;
                    const max = this.maxScroll();
                    let next = this.screen.scrollTop;

                    if (event.key === 'ArrowDown') next += 85;
                    if (event.key === 'ArrowUp') next -= 85;
                    if (event.key === 'PageDown' || (event.key === ' ' && !event.shiftKey)) next += step;
                    if (event.key === 'PageUp' || (event.key === ' ' && event.shiftKey)) next -= step;
                    if (event.key === 'Home') next = 0;
                    if (event.key === 'End') next = max;

                    next = Math.max(0, Math.min(max, next));

                    if (Math.abs(next - this.screen.scrollTop) < 0.5) {
                        return;
                    }

                    event.preventDefault();
                    this.goTo(next, false);
                };

                this.screen.addEventListener('scroll', this.scrollHandler, { passive: true });
                this.screen.addEventListener('mouseenter', this.mouseEnterHandler);
                this.screen.addEventListener('mouseleave', this.mouseLeaveHandler);
                this.screen.addEventListener('pointerdown', this.pointerDownHandler);
                this.screen.addEventListener('pointermove', this.pointerMoveHandler);
                this.screen.addEventListener('pointerup', this.pointerUpHandler);
                this.screen.addEventListener('pointercancel', this.pointerUpHandler);
                this.screen.addEventListener('keydown', this.keyHandler);
                this.pageImage.addEventListener('load', this.imageLoadHandler);

                if ('ResizeObserver' in window) {
                    this.resizeObserver = new ResizeObserver(() => this.refreshDimensions());
                    this.resizeObserver.observe(this.screen);
                    this.resizeObserver.observe(this.screenContent);
                }

                this.refreshDimensions();
            });
        },

        maxScroll() {
            if (!this.screen) {
                return 0;
            }

            return Math.max(0, this.screen.scrollHeight - this.screen.clientHeight);
        },

        goTo(position, immediate = false) {
            if (this.lenis) {
                this.lenis.scrollTo(position, {
                    immediate,
                    duration: 0.9,
                    easing: (t) => 1 - Math.pow(1 - t, 3),
                });
            } else if (this.screen) {
                this.screen.scrollTo({ top: position, behavior: 'auto' });
            }

            if (immediate) {
                this.handleScroll();
            }
        },

        handleScroll() {
            if (!this.screen) {
                return;
            }

            const current = this.screen.scrollTop;

            if (Math.abs(current - this.lastScroll) > 0.5) {
                this.hasInteracted = true;
            }

            this.lastScroll = current;
            this.updateProgress();
        },

        updateProgress() {
            if (!this.screen) {
                return;
            }

            const max = this.maxScroll();
            this.canScroll = max > 1;
            this.progressPercent = max > 0
                ? Math.round(Math.max(0, Math.min(1, this.screen.scrollTop / max)) * 100)
                : 0;
        },

        refreshDimensions() {
            if (this.destroyed) {
                return;
            }

            this.lenis?.resize();
            this.updateProgress();
        },

        destroy() {
            this.destroyed = true;
            this.resizeObserver?.disconnect();
            this.resizeObserver = null;

            if (this.screen) {
                this.screen.removeEventListener('scroll', this.scrollHandler);
                this.screen.removeEventListener('mouseenter', this.mouseEnterHandler);
                this.screen.removeEventListener('mouseleave', this.mouseLeaveHandler);
                this.screen.removeEventListener('pointerdown', this.pointerDownHandler);
                this.screen.removeEventListener('pointermove', this.pointerMoveHandler);
                this.screen.removeEventListener('pointerup', this.pointerUpHandler);
                this.screen.removeEventListener('pointercancel', this.pointerUpHandler);
                this.screen.removeEventListener('keydown', this.keyHandler);
            }

            if (this.pageImage) {
                this.pageImage.removeEventListener('load', this.imageLoadHandler);
            }

            if (typeof this.unsubscribeLenis === 'function') {
                this.unsubscribeLenis();
            }

            this.lenis?.destroy();
            this.lenis = null;
            this.unsubscribeLenis = null;
            this.screen = null;
            this.screenContent = null;
            this.pageImage = null;
            this.isDragging = false;
            this.activePointerId = null;
        },
    }));
});
