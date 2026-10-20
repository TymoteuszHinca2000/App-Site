
import Swiper from 'swiper';
import { A11y, Navigation } from 'swiper/modules';
import 'swiper/css';

document.addEventListener('alpine:init', () => {
    Alpine.data('alpRelatedBlogs', () => ({
        swiper: null,
        resizeObserver: null,
        resizeHandler: null,
        imageLoadHandler: null,
        images: [],
        frame: null,
        lastWidth: 0,
        destroyed: false,

        init() {
            this.destroyed = false;

            this.$nextTick(() => {
                if (this.destroyed || this.swiper) {
                    return;
                }

                const container = this.$refs.swiper;
                const prev = this.$refs.prev;
                const next = this.$refs.next;

                if (container && prev && next) {
                    this.swiper = new Swiper(container, {
                        modules: [Navigation, A11y],
                        slidesPerView: 1.05,
                        spaceBetween: 16,
                        speed: 650,
                        grabCursor: true,
                        watchOverflow: true,
                        autoHeight: false,
                        noSwiping: true,
                        noSwipingSelector: 'a, button',
                        preventClicks: false,
                        preventClicksPropagation: false,
                        touchStartPreventDefault: false,
                        navigation: {
                            prevEl: prev,
                            nextEl: next,
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 1.4,
                                spaceBetween: 18,
                            },
                            768: {
                                slidesPerView: 2,
                                spaceBetween: 20,
                            },
                            1024: {
                                slidesPerView: 2.5,
                                spaceBetween: 22,
                            },
                            1280: {
                                slidesPerView: 3,
                                spaceBetween: 24,
                            },
                        },
                        on: {
                            resize: () => this.scheduleEqualize(),
                            breakpoint: () => this.scheduleEqualize(),
                        },
                    });
                }

                this.resizeHandler = () => this.scheduleEqualize();

                window.addEventListener('resize', this.resizeHandler, {
                    passive: true,
                });

                if ('ResizeObserver' in window) {
                    const observedElement = container || this.$refs.cards;

                    if (observedElement) {
                        this.resizeObserver = new ResizeObserver((entries) => {
                            const width = Math.round(entries[0]?.contentRect.width ?? 0);

                            if (width && width !== this.lastWidth) {
                                this.lastWidth = width;
                                this.scheduleEqualize();
                            }
                        });

                        this.resizeObserver.observe(observedElement);
                    }
                }

                this.imageLoadHandler = () => this.scheduleEqualize();

                this.images = Array.from(
                    this.$el.querySelectorAll('[data-related-blog-card] img')
                );

                this.images.forEach((image) => {
                    if (!image.complete) {
                        image.addEventListener('load', this.imageLoadHandler);
                    }
                });

                if (document.fonts?.ready) {
                    document.fonts.ready.then(() => {
                        if (!this.destroyed && this.$el.isConnected) {
                            this.scheduleEqualize();
                        }
                    });
                }

                this.scheduleEqualize();
            });
        },

        scheduleEqualize() {
            if (this.destroyed) {
                return;
            }

            if (this.frame !== null) {
                cancelAnimationFrame(this.frame);
            }

            this.frame = requestAnimationFrame(() => {
                this.frame = requestAnimationFrame(() => {
                    this.frame = null;
                    this.equalizeCards();
                });
            });
        },

        equalizeCards() {
            if (this.destroyed || !this.$el.isConnected) {
                return;
            }

            const cards = Array.from(
                this.$el.querySelectorAll('[data-related-blog-card]')
            );

            if (!cards.length) {
                return;
            }

            cards.forEach((card) => {
                card.style.minHeight = '';
            });

            const maxHeight = Math.ceil(
                Math.max(...cards.map((card) => card.scrollHeight))
            );

            cards.forEach((card) => {
                card.style.minHeight = `${maxHeight}px`;
            });
        },

        destroy() {
            this.destroyed = true;

            if (this.frame !== null) {
                cancelAnimationFrame(this.frame);
                this.frame = null;
            }

            if (this.resizeHandler) {
                window.removeEventListener('resize', this.resizeHandler);
                this.resizeHandler = null;
            }

            if (this.resizeObserver) {
                this.resizeObserver.disconnect();
                this.resizeObserver = null;
            }

            if (this.imageLoadHandler) {
                this.images.forEach((image) => {
                    image.removeEventListener('load', this.imageLoadHandler);
                });
            }

            this.images = [];
            this.imageLoadHandler = null;

            if (this.swiper && !this.swiper.destroyed) {
                this.swiper.destroy(true, true);
            }

            this.swiper = null;
        },
    }));
});
