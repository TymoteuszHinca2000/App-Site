document.addEventListener('alpine:init', () => {
    Alpine.data('alpBlogContent', () => ({
        copiedIndex: null,
        copyTimer: null,

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