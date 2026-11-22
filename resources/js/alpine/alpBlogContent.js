import Swiper from 'swiper';
import { A11y, Navigation } from 'swiper/modules';
import 'swiper/css';

document.addEventListener('alpine:init', () => {
    Alpine.data('alpBlogContent', () => ({
        copiedIndex: null,
        copyTimer: null,
        promoSwiper: null,
        destroyed: false,

        init() {
            this.$nextTick(() => {
                const container = this.$refs.promoSlider;

                if (this.destroyed || !container || this.promoSwiper) {
                    return;
                }

                this.promoSwiper = new Swiper(container, {
                    modules: [A11y, Navigation],
                    slidesPerView: 1,
                    slidesPerGroup: 1,
                    spaceBetween: 12,
                    speed: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 650,
                    grabCursor: true,
                    watchOverflow: true,
                    noSwipingSelector: 'a, button, code',
                    preventClicks: false,
                    preventClicksPropagation: false,
                    touchStartPreventDefault: false,
                    navigation: {
                        prevEl: this.$refs.promoPrev,
                        nextEl: this.$refs.promoNext,
                    },
                    a11y: {
                        prevSlideMessage: this.$refs.promoPrev.getAttribute('aria-label'),
                        nextSlideMessage: this.$refs.promoNext.getAttribute('aria-label'),
                    },
                    breakpoints: {
                        640: { slidesPerView: 2 },
                        1024: { slidesPerView: 4 },
                    },
                });
            });
        },

        async copyUrl(url, index) {
            if (!url) {
                return;
            }

            let success = false;

            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(url);
                    success = true;
                } else {
                    const textarea = document.createElement('textarea');

                    textarea.value = url;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';

                    document.body.appendChild(textarea);
                    textarea.select();

                    success = document.execCommand('copy');

                    textarea.remove();
                }
            } catch (error) {
                success = false;
            }

            if (!success) {
                return;
            }

            this.copiedIndex = index;

            if (this.copyTimer) {
                window.clearTimeout(this.copyTimer);
            }

            this.copyTimer = window.setTimeout(() => {
                this.copiedIndex = null;
                this.copyTimer = null;
            }, 2500);
        },

        destroy() {
            this.destroyed = true;
            this.promoSwiper?.destroy(true, true);
            this.promoSwiper = null;

            if (this.copyTimer) {
                window.clearTimeout(this.copyTimer);
                this.copyTimer = null;
            }
        },
    }));

    Alpine.data('alpBlogCompare', () => ({
        position: 50,
    }));
});
