import Swiper from 'swiper';
import { A11y, Navigation } from 'swiper/modules';

import 'swiper/css';

document.addEventListener('alpine:init', () => {
    Alpine.data('alpRelatedOffers', () => ({
        swiper: null,

        init() {
            this.$nextTick(() => {
                this.swiper = new Swiper(this.$refs.swiper, {
                    modules: [
                        Navigation,
                        A11y,
                    ],

                    slidesPerView: 1.05,
                    spaceBetween: 16,

                    speed: 650,

                    grabCursor: true,

                    watchOverflow: true,

                    navigation: {
                        prevEl: this.$refs.prev,
                        nextEl: this.$refs.next,
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
                });
            });
        },

        destroy() {
            if (!this.swiper) {
                return;
            }

            this.swiper.destroy(true, true);

            this.swiper = null;
        },
    }));
});