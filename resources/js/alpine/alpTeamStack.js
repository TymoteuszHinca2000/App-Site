import Swiper from 'swiper';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';

document.addEventListener('alpine:init', () => {
    Alpine.data('alpTeamStack', () => ({
        swiper: null,

        init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            const container = this.$el;
            const track = container.querySelector('.swiper-wrapper');
            if (!track || track.children.length < 2) {
                return;
            }

            const slides = [...track.children];
            const style = getComputedStyle(container);
            const visibleLimit = Number(style.getPropertyValue('--team-stack-limit'));

            // Swiper needs at least one slide beyond the visible ones to loop.
            while (track.children.length <= visibleLimit) {
                slides.forEach((slide) => {
                    const copy = slide.cloneNode(true);
                    copy.classList.add('team-stack-copy');
                    copy.setAttribute('aria-hidden', 'true');
                    track.append(copy);
                });
            }

            this.swiper = new Swiper(container, {
                modules: [Autoplay],
                slidesPerView: 'auto',
                slidesPerGroup: 1,
                spaceBetween: parseFloat(style.getPropertyValue('--team-stack-gap')),
                speed: 650,
                loop: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                    reverseDirection: false,
                },
            });
        },

        destroy() {
            this.swiper?.destroy(true, true);
            this.$el.querySelectorAll('.team-stack-copy').forEach((copy) => copy.remove());
        },
    }));
});
