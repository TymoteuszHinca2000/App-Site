const blobShapes = [
    'M 112 320 C 45 260 95 116 230 110 C 339 104 395 30 515 86 C 623 135 624 196 755 193 C 890 189 901 325 841 416 C 797 482 884 604 756 665 C 655 718 557 635 430 715 C 328 782 243 645 151 613 C 58 582 27 428 112 320 Z',
    'M 89 288 C 54 183 161 128 245 108 C 360 80 432 65 515 115 C 609 171 681 121 764 215 C 854 316 876 361 807 474 C 737 586 868 632 723 681 C 603 721 506 693 388 700 C 266 704 263 628 151 580 C 41 533 10 382 89 288 Z',
    'M 130 328 C 47 273 74 179 237 127 C 349 93 364 48 481 76 C 619 108 657 210 755 206 C 855 202 926 327 860 425 C 803 511 853 625 726 686 C 622 738 520 628 431 682 C 320 743 217 669 147 598 C 35 499 65 398 130 328 Z',
];

const shapePoints = blobShapes.map((shape) =>
    [...shape.matchAll(/-?\d+(?:\.\d+)?/g)].map((match) => Number(match[0]))
);

const SHAPE_DURATION = 6000;
const FRAME_INTERVAL = 40;
const TOTAL_DURATION = SHAPE_DURATION * shapePoints.length;

const easeInOut = (value) => {
    return value * value * (3 - 2 * value);
};

const buildBlobPath = (points) => {
    let path = `M ${points[0].toFixed(1)} ${points[1].toFixed(1)}`;

    for (let index = 2; index < points.length; index += 6) {
        const segment = points
            .slice(index, index + 6)
            .map((value) => value.toFixed(1))
            .join(' ');

        path += ` C ${segment}`;
    }

    return `${path} Z`;
};

document.addEventListener('alpine:init', () => {
    Alpine.data('alpAboutCompany', () => ({
    frameId: null,
    observer: null,
    motionQuery: null,
    motionListener: null,
    visibilityListener: null,
    inView: false,
    elapsed: 0,
    startTime: 0,
    lastRender: 0,

    init() {
        this.motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

        this.visibilityListener = () => {
            if (document.hidden) {
                this.pause();
            } else {
                this.resume();
            }
        };

        document.addEventListener(
            'visibilitychange',
            this.visibilityListener
        );

        this.motionListener = (event) => {
            if (event.matches) {
                this.pause();
                this.elapsed = 0;

                if (this.$refs.blobPath) {
                    this.$refs.blobPath.setAttribute('d', blobShapes[0]);
                }
            } else {
                this.resume();
            }
        };

        this.motionQuery.addEventListener(
            'change',
            this.motionListener
        );

        this.observer = new IntersectionObserver(
            (entries) => {
                this.inView = entries[0].isIntersecting;

                if (this.inView) {
                    this.resume();
                } else {
                    this.pause();
                }
            },
            {
                threshold: 0,
                rootMargin: '100px 0px',
            }
        );

        this.$nextTick(() => {
            if (this.observer && this.$el.isConnected) {
                this.observer.observe(this.$el);
            }
        });
    },

    resume() {
        if (
            this.frameId !== null ||
            !this.inView ||
            document.hidden ||
            this.motionQuery?.matches ||
            !this.$refs.blobPath
        ) {
            return;
        }

        this.startTime = performance.now() - this.elapsed;
        this.lastRender = 0;

        this.frameId = requestAnimationFrame((time) => {
            this.render(time);
        });
    },

    pause() {
        if (this.frameId === null) {
            return;
        }

        cancelAnimationFrame(this.frameId);

        this.frameId = null;
        this.elapsed = performance.now() - this.startTime;
    },

    render(time) {
        this.frameId = null;

        if (
            !this.inView ||
            document.hidden ||
            this.motionQuery?.matches
        ) {
            return;
        }

        if (time - this.lastRender >= FRAME_INTERVAL) {
            const elapsed = (time - this.startTime) % TOTAL_DURATION;

            const shapeIndex = Math.floor(
                elapsed / SHAPE_DURATION
            );

            const nextIndex =
                (shapeIndex + 1) % shapePoints.length;

            const progress =
                (elapsed % SHAPE_DURATION) / SHAPE_DURATION;

            const easedProgress = easeInOut(progress);

            const currentShape = shapePoints[shapeIndex];
            const nextShape = shapePoints[nextIndex];

            const interpolated = currentShape.map(
                (value, index) => {
                    return value +
                        (nextShape[index] - value) * easedProgress;
                }
            );

            const path = buildBlobPath(interpolated);

            if (this.$refs.blobPath) {
                this.$refs.blobPath.setAttribute('d', path);
            }

            this.lastRender = time;
        }

        this.frameId = requestAnimationFrame((nextTime) => {
            this.render(nextTime);
        });
    },

    destroy() {
        this.pause();

        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }

        if (this.visibilityListener) {
            document.removeEventListener(
                'visibilitychange',
                this.visibilityListener
            );

            this.visibilityListener = null;
        }

        if (this.motionQuery && this.motionListener) {
            this.motionQuery.removeEventListener(
                'change',
                this.motionListener
            );

            this.motionListener = null;
        }
    },
}));
});
