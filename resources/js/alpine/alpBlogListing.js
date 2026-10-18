
import Swiper from 'swiper';
import { A11y, Navigation } from 'swiper/modules';
import 'swiper/css';

document.addEventListener('alpine:init', () => {
    Alpine.data('alpBlogListing', () => ({
        featuredSwiper: null,
        categoryResizeObserver: null,
        categoryScrollHandler: null,
        resizeHandler: null,

        canScrollPrev: false,
        canScrollNext: false,

        init() {
            this.$nextTick(() => {
                if (this.$refs.featuredSlider) {
                    this.featuredSwiper = new Swiper(this.$refs.featuredSlider, {
                        modules: [Navigation, A11y],
                        slidesPerView: 1,
                        spaceBetween: 0,
                        speed: 700,
                        loop: true,
                        grabCursor: true,
                        navigation: {
                            prevEl: this.$refs.featuredPrev,
                            nextEl: this.$refs.featuredNext,
                        },
                    });
                }

                const track = this.$refs.categoryTrack;

                if (!track) {
                    return;
                }

                this.categoryScrollHandler = () => {
                    this.updateCategoryNavigation();
                };

                this.resizeHandler = () => {
                    this.updateCategoryNavigation();
                };

                track.addEventListener('scroll', this.categoryScrollHandler, {
                    passive: true,
                });

                window.addEventListener('resize', this.resizeHandler, {
                    passive: true,
                });

                if ('ResizeObserver' in window) {
                    this.categoryResizeObserver = new ResizeObserver(() => {
                        this.updateCategoryNavigation();
                    });

                    this.categoryResizeObserver.observe(track);

                    if (track.firstElementChild) {
                        this.categoryResizeObserver.observe(track.firstElementChild);
                    }
                }

                this.ensureActiveCategoryVisible();
                this.updateCategoryNavigation();
            });
        },

        updateCategoryNavigation() {
            const track = this.$refs.categoryTrack;

            if (!track || track.clientWidth === 0) {
                this.canScrollPrev = false;
                this.canScrollNext = false;
                return;
            }

            const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);

            this.canScrollPrev = track.scrollLeft > 2;
            this.canScrollNext = track.scrollLeft < maxScroll - 2;
        },

        scrollCategories(direction) {
            const track = this.$refs.categoryTrack;

            if (!track || track.clientWidth === 0) {
                return;
            }

            const amount = Math.max(160, Math.round(track.clientWidth * 0.75));
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            track.scrollBy({
                left: direction * amount,
                behavior: reduceMotion ? 'auto' : 'smooth',
            });
        },

        ensureActiveCategoryVisible() {
            const track = this.$refs.categoryTrack;
            const active = this.$refs.activeCategory;

            if (!track || !active || track.clientWidth === 0) {
                return;
            }

            const left = active.offsetLeft;
            const right = left + active.offsetWidth;
            const visibleLeft = track.scrollLeft;
            const visibleRight = visibleLeft + track.clientWidth;

            if (left < visibleLeft) {
                track.scrollLeft = Math.max(0, left - 12);
            } else if (right > visibleRight) {
                track.scrollLeft = right - track.clientWidth + 12;
            }
        },

        changeCategory(url) {
            if (url) {
                window.location.assign(url);
            }
        },

        destroy() {
            if (this.featuredSwiper) {
                this.featuredSwiper.destroy(true, true);
                this.featuredSwiper = null;
            }

            const track = this.$refs.categoryTrack;

            if (track && this.categoryScrollHandler) {
                track.removeEventListener('scroll', this.categoryScrollHandler);
            }

            if (this.resizeHandler) {
                window.removeEventListener('resize', this.resizeHandler);
            }

            if (this.categoryResizeObserver) {
                this.categoryResizeObserver.disconnect();
                this.categoryResizeObserver = null;
            }

            this.categoryScrollHandler = null;
            this.resizeHandler = null;
        },
    }));
});
