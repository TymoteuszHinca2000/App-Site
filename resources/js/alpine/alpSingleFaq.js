document.addEventListener('alpine:init', () => {
    Alpine.data('alpSingleFaq', () => ({
        activeFaq: null,

        toggleFaq(index) {
            if (this.activeFaq === index) {
                this.activeFaq = null;

                return;
            }

            this.activeFaq = index;
        },

        isOpen(index) {
            return this.activeFaq === index;
        },
    }));
});