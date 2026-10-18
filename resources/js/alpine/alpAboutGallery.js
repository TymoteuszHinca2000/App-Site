import GLightbox from 'glightbox';

document.addEventListener('alpine:init', () => {
    Alpine.data('alpAboutGallery', () => ({
        lightbox: null,
        lastTrigger: null,

        init() {
            const links = Array.from(
                this.$el.querySelectorAll('[data-about-gallery-image]')
            );

            if (!links.length) {
                return;
            }

            const elements = links.map((link) => ({
                href: link.href,
                type: 'image',
            }));

            this.lightbox = GLightbox({
                elements,
                loop: elements.length > 1,
                touchNavigation: true,
                keyboardNavigation: true,
                closeOnOutsideClick: true,
                openEffect: 'zoom',
                closeEffect: 'fade',
                slideEffect: 'slide',
            });

            this.lightbox.on('open', () => {
                const container = document.querySelector('.glightbox-container');

                container?.classList.add('appandsite-about-gallery-lightbox');

                requestAnimationFrame(() => {
                    container?.querySelector('.gclose')?.focus({
                        preventScroll: true,
                    });
                });
            });

            this.lightbox.on('close', () => {
                const trigger = this.lastTrigger;

                requestAnimationFrame(() => {
                    if (trigger?.isConnected) {
                        trigger.focus({ preventScroll: true });
                    }

                    this.lastTrigger = null;
                });
            });
        },

        open(index) {
            if (!this.lightbox) {
                return;
            }

            const links = this.$el.querySelectorAll('[data-about-gallery-image]');
            const trigger = links[index];

            this.lastTrigger = trigger || null;

            if (trigger && document.activeElement === trigger) {
                trigger.blur();
            }

            this.lightbox.openAt(index);
        },

        destroy() {
            if (this.lightbox) {
                this.lightbox.destroy();
                this.lightbox = null;
            }

            this.lastTrigger = null;
        },
    }));
});