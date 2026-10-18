document.addEventListener('alpine:init', () => {
    Alpine.data('alpHeader', () => ({
        scrolled: false,
        mobileOpen: false,
        languageOpen: false,
        mobileLanguageOpen: false,
        scrollHandler: null,
        resizeHandler: null,
        scrollPosition: 0,
        htmlOverflow: '',
        bodyOverflow: '',
        bodyPosition: '',
        bodyTop: '',
        bodyLeft: '',
        bodyRight: '',
        bodyWidth: '',

        init() {
            this.scrollHandler = () => {
                this.scrolled = window.scrollY > 20;
            };

            this.resizeHandler = () => {
                if (window.innerWidth >= 1280 && this.mobileOpen) {
                    this.setMobileState(false);
                }

                if (window.innerWidth < 1280) {
                    this.languageOpen = false;
                }
            };

            this.scrollHandler();
            this.resizeHandler();

            window.addEventListener('scroll', this.scrollHandler, {
                passive: true,
            });

            window.addEventListener('resize', this.resizeHandler, {
                passive: true,
            });
        },

        toggleMobile() {
            this.setMobileState(!this.mobileOpen);
            this.languageOpen = false;
        },

        setMobileState(isOpen) {
            if (isOpen === this.mobileOpen) {
                return;
            }

            this.mobileOpen = isOpen;

            if (isOpen) {
                this.scrollPosition = window.scrollY;

                this.htmlOverflow = document.documentElement.style.overflow;
                this.bodyOverflow = document.body.style.overflow;
                this.bodyPosition = document.body.style.position;
                this.bodyTop = document.body.style.top;
                this.bodyLeft = document.body.style.left;
                this.bodyRight = document.body.style.right;
                this.bodyWidth = document.body.style.width;

                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.top = `-${this.scrollPosition}px`;
                document.body.style.left = '0';
                document.body.style.right = '0';
                document.body.style.width = '100%';

                document.documentElement.classList.add('menu-open');
                document.body.classList.add('menu-open');

                window.dispatchEvent(
                    new CustomEvent('app:menu-lock', {
                        detail: {
                            locked: true,
                        },
                    })
                );

                return;
            }

            document.documentElement.style.overflow = this.htmlOverflow;
            document.body.style.overflow = this.bodyOverflow;
            document.body.style.position = this.bodyPosition;
            document.body.style.top = this.bodyTop;
            document.body.style.left = this.bodyLeft;
            document.body.style.right = this.bodyRight;
            document.body.style.width = this.bodyWidth;

            document.documentElement.classList.remove('menu-open');
            document.body.classList.remove('menu-open');

            window.dispatchEvent(
                new CustomEvent('app:menu-lock', {
                    detail: {
                        locked: false,
                    },
                })
            );

            window.scrollTo(0, this.scrollPosition);

            this.mobileLanguageOpen = false;
        },

        closeMenus() {
            this.setMobileState(false);
            this.languageOpen = false;
            this.mobileLanguageOpen = false;
        },

        destroy() {
            if (this.mobileOpen) {
                this.setMobileState(false);
            }

            if (this.scrollHandler) {
                window.removeEventListener('scroll', this.scrollHandler);
            }

            if (this.resizeHandler) {
                window.removeEventListener('resize', this.resizeHandler);
            }
        },
    }));
});