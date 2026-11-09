document.addEventListener('alpine:init', () => {
    Alpine.data('alpContactSection', () => ({
        copiedIndex: null,
        copyTimeout: null,
        textareas: [],
        updateTextareaScroll: null,
        textareaForm: null,
        resetTextareaScroll: null,

        init() {
            this.$nextTick(() => {
                this.placeFormNote();
                this.initTextareaScrolling();
            });
        },

        placeFormNote() {
            const note = this.$refs.formNote;
            const actions = this.$el.querySelector('.wpcf7-form .app-contact-actions');

            if (!note || !actions) {
                return;
            }

            actions.appendChild(note);
            note.classList.add('app-contact-note-in-actions');
        },


        initTextareaScrolling() {
            this.textareas = Array.from(this.$el.querySelectorAll('.wpcf7-form textarea'));

            if (!this.textareas.length) {
                return;
            }

            this.updateTextareaScroll = () => {
                this.textareas.forEach((textarea) => {
                    const isScrollable = textarea.scrollHeight > textarea.clientHeight + 1;
                    textarea.toggleAttribute('data-lenis-prevent', isScrollable);
                });
            };

            this.textareas.forEach((textarea) => {
                textarea.addEventListener('input', this.updateTextareaScroll);
            });

            window.addEventListener('resize', this.updateTextareaScroll);

            this.textareaForm = this.$el.querySelector('.wpcf7-form');
            this.resetTextareaScroll = () => {
                window.requestAnimationFrame(this.updateTextareaScroll);
            };

            this.textareaForm?.addEventListener('reset', this.resetTextareaScroll);
            this.updateTextareaScroll();
        },

        async copyText(value, index) {
            if (!value) {
                return;
            }

            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(value);
                } else {
                    const field = document.createElement('textarea');
                    field.value = value;
                    field.setAttribute('readonly', '');
                    field.style.position = 'fixed';
                    field.style.opacity = '0';
                    document.body.appendChild(field);
                    field.select();

                    const copied = document.execCommand('copy');
                    field.remove();

                    if (!copied) {
                        return;
                    }
                }

                this.copiedIndex = index;
                window.clearTimeout(this.copyTimeout);
                this.copyTimeout = window.setTimeout(() => {
                    this.copiedIndex = null;
                }, 1800);
            } catch (error) {
                this.copiedIndex = null;
            }
        },

        destroy() {
            window.clearTimeout(this.copyTimeout);
            this.copyTimeout = null;

            if (this.updateTextareaScroll) {
                this.textareas.forEach((textarea) => {
                    textarea.removeEventListener('input', this.updateTextareaScroll);
                    textarea.removeAttribute('data-lenis-prevent');
                });

                window.removeEventListener('resize', this.updateTextareaScroll);
                this.textareaForm?.removeEventListener('reset', this.resetTextareaScroll);
            }
        },
    }));
});
