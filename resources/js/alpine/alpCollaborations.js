import Swiper from 'swiper';
import { A11y, Autoplay, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/a11y';
import 'swiper/css/pagination';

function copyWithSelection(value, container) {
    const textarea = document.createElement('textarea');
    const focusedElement = document.activeElement;

    textarea.value = value;
    textarea.readOnly = true;
    textarea.tabIndex = -1;
    textarea.style.cssText = 'position:fixed;left:-9999px;top:0;opacity:0;';

    try {
        (container || document.body).appendChild(textarea);
        textarea.focus({ preventScroll: true });
        textarea.select();
        textarea.setSelectionRange(0, value.length);

        return document.execCommand('copy');
    } catch {
        return false;
    } finally {
        textarea.remove();

        if (focusedElement?.isConnected) {
            try {
                focusedElement.focus({ preventScroll: true });
            } catch {
                // Copy feedback should still update if the old focus cannot be restored.
            }
        }
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('alpCollaborationCard', () => ({
        copied: false,
        copyError: false,
        copyTimer: null,
        copyAttempt: 0,
        destroyed: false,

        async copyCode(code) {
            const value = String(code ?? '');

            if (!value || this.destroyed) {
                return;
            }

            const attempt = ++this.copyAttempt;
            window.clearTimeout(this.copyTimer);
            this.copyTimer = null;
            this.copied = false;
            this.copyError = false;
            let success = false;

            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(value);
                    success = true;
                }
            } catch {
                // Clipboard permissions can fail even on a secure connection.
            }

            if (this.destroyed || attempt !== this.copyAttempt) {
                return;
            }

            if (!success) {
                success = copyWithSelection(value, this.$el);
            }

            this.copied = success;
            this.copyError = !success;
            this.copyTimer = window.setTimeout(() => {
                this.copied = false;
                this.copyError = false;
                this.copyTimer = null;
            }, 3000);
        },

        destroy() {
            this.destroyed = true;
            this.copyAttempt += 1;
            window.clearTimeout(this.copyTimer);
            this.copyTimer = null;
        },
    }));

    Alpine.data('alpCollaborations', (labels = {}) => ({
        swiper: null,
        reducedMotion: false,
        motionQuery: null,
        motionListener: null,
        destroyed: false,

        init() {
            this.$nextTick(() => {
                if (this.destroyed || this.swiper) return;

                const container = this.$refs.swiper;

                if (!container || container.querySelectorAll('.swiper-slide').length < 2) {
                    return;
                }

                this.motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
                this.reducedMotion = this.motionQuery.matches;
                this.motionListener = (event) => {
                    this.reducedMotion = event.matches;

                    if (this.swiper && !this.swiper.destroyed) {
                        this.swiper.params.speed = this.reducedMotion ? 0 : 650;
                    }
                };
                this.motionQuery.addEventListener('change', this.motionListener);

                this.swiper = new Swiper(container, {
                    modules: [Navigation, Pagination, Autoplay, A11y],
                    slidesPerView: 1,
                    slidesPerGroup: 1,
                    spaceBetween: 24,
                    speed: this.reducedMotion ? 0 : 650,
                    loop: true,
                    rewind: false,
                    watchOverflow: true,
                    noSwipingSelector: 'a, button, input, select, textarea',
                    preventClicks: false,
                    preventClicksPropagation: false,
                    touchStartPreventDefault: false,
                    navigation: {
                        prevEl: this.$refs.prev,
                        nextEl: this.$refs.next,
                    },
                    pagination: {
                        el: this.$refs.pagination,
                        clickable: true,
                        bulletElement: 'button',
                        renderBullet: (index, className) => {
                            const bullet = document.createElement('button');
                            bullet.type = 'button';
                            bullet.className = className;
                            bullet.setAttribute('aria-label', String(
                                labels.paginationBulletMessage || 'Przejdź do slajdu {{index}}'
                            ).replace('{{index}}', String(index + 1)));

                            return bullet.outerHTML;
                        },
                    },
                    autoplay: {
                        enabled: true,
                        delay: 5000,
                        reverseDirection: false,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: false,
                        waitForTransition: false,
                        stopOnLastSlide: false,
                    },
                    a11y: { ...labels },
                });
            });
        },

        destroy() {
            this.destroyed = true;

            if (this.motionQuery && this.motionListener) {
                this.motionQuery.removeEventListener('change', this.motionListener);
            }

            this.motionQuery = null;
            this.motionListener = null;

            if (this.swiper && !this.swiper.destroyed) {
                this.swiper.destroy(true, true);
            }

            this.swiper = null;
        },
    }));
});
