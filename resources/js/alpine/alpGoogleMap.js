import { setOptions, importLibrary } from '@googlemaps/js-api-loader';

const MAP_STYLES = [
    { elementType: 'geometry', stylers: [{ color: '#f1f8f8' }] },
    { elementType: 'labels.text.fill', stylers: [{ color: '#617789' }] },
    { elementType: 'labels.text.stroke', stylers: [{ color: '#ffffff' }] },
    { featureType: 'administrative', elementType: 'geometry.stroke', stylers: [{ color: '#d4e4e8' }] },
    { featureType: 'landscape', elementType: 'geometry.fill', stylers: [{ color: '#f4f9f9' }] },
    { featureType: 'landscape.man_made', elementType: 'geometry.fill', stylers: [{ color: '#f0f5f7' }] },
    { featureType: 'poi', stylers: [{ visibility: 'off' }] },
    { featureType: 'transit', stylers: [{ visibility: 'off' }] },
    { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
    { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: '#dce9ec' }] },
    { featureType: 'road.highway', elementType: 'geometry.fill', stylers: [{ color: '#d8eeeb' }] },
    { featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [{ color: '#badcd6' }] },
    { featureType: 'road.local', elementType: 'labels', stylers: [{ visibility: 'simplified' }] },
    { featureType: 'water', elementType: 'geometry.fill', stylers: [{ color: '#ccece9' }] },
    { featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#689e9e' }] },
    { featureType: 'administrative.locality', elementType: 'labels.text.fill', stylers: [{ color: '#06283b' }] },
];

let loaderIsConfigured = false;

async function loadGoogleMaps(apiKey, language) {
    if (!loaderIsConfigured) {
        setOptions({
            key: apiKey,
            v: 'weekly',
            language,
        });
        loaderIsConfigured = true;
    }

    return importLibrary('maps');
}

document.addEventListener('alpine:init', () => {
    Alpine.data('alpGoogleMap', (config = {}) => ({
        map: null,
        markers: [],
        observer: null,
        activeIndex: 0,
        status: 'loading',
        initialized: false,
        destroyed: false,
        config,
        locations: Array.isArray(config.locations) ? config.locations : [],

        get activeLocation() {
            return this.locations[this.activeIndex] || {
                title: '',
                address: '',
                directionsUrl: '#',
            };
        },

        init() {
            if (!this.config.apiKey || !this.locations.length || !this.$refs.map) {
                this.status = 'error';
                return;
            }

            if (!('IntersectionObserver' in window)) {
                this.load();
                return;
            }

            this.observer = new IntersectionObserver((entries) => {
                if (!entries.some((entry) => entry.isIntersecting)) {
                    return;
                }

                this.observer?.disconnect();
                this.observer = null;
                this.load();
            }, {
                rootMargin: '320px 0px',
                threshold: 0,
            });

            this.observer.observe(this.$refs.map);
        },

        async load() {
            if (this.initialized || this.destroyed) {
                return;
            }

            this.initialized = true;
            this.status = 'loading';

            try {
                const { Map } = await loadGoogleMaps(
                    this.config.apiKey,
                    this.config.language || 'pl'
                );

                if (this.destroyed || !this.$refs.map) {
                    return;
                }

                const first = this.locations[0];

                this.map = new Map(this.$refs.map, {
                    center: { lat: first.lat, lng: first.lng },
                    zoom: Number(this.config.zoom) || 13,
                    minZoom: 3,
                    maxZoom: 19,
                    disableDefaultUI: true,
                    gestureHandling: window.matchMedia('(pointer: coarse)').matches ? 'cooperative' : 'greedy',
                    clickableIcons: false,
                    keyboardShortcuts: true,
                    styles: MAP_STYLES,
                    backgroundColor: '#f1f8f8',
                });

                const map = this.map;
                const template = this.$refs.markerTemplate;

                class BrandMarker extends google.maps.OverlayView {
                    constructor(position, element, onClick) {
                        super();
                        this.position = position;
                        this.element = element;
                        this.onClick = onClick;
                        this.element.addEventListener('click', this.onClick);
                        this.setMap(map);
                    }

                    onAdd() {
                        this.getPanes()?.overlayMouseTarget.appendChild(this.element);
                    }

                    draw() {
                        const projection = this.getProjection();
                        const point = projection?.fromLatLngToDivPixel(this.position);

                        if (point) {
                            this.element.style.left = `${point.x}px`;
                            this.element.style.top = `${point.y}px`;
                        }
                    }

                    onRemove() {
                        this.element.removeEventListener('click', this.onClick);
                        this.element.remove();
                    }
                }

                this.markers = this.locations.map((location, index) => {
                    const element = template.content.firstElementChild.cloneNode(true);
                    const button = element.querySelector('[data-map-marker-button]');
                    button?.setAttribute('aria-label', location.title);
                    button?.setAttribute('title', location.title);

                    return new BrandMarker(
                        new google.maps.LatLng(location.lat, location.lng),
                        element,
                        () => this.selectLocation(index)
                    );
                });

                if (this.locations.length > 1) {
                    const bounds = new google.maps.LatLngBounds();

                    this.locations.forEach((location) => {
                        bounds.extend({ lat: location.lat, lng: location.lng });
                    });

                    map.fitBounds(bounds, 72);
                    google.maps.event.addListenerOnce(map, 'idle', () => {
                        if (!this.destroyed && map.getZoom() > 15) {
                            map.setZoom(15);
                        }
                    });
                }

                this.updateMarkers();
                this.status = 'ready';
            } catch (error) {
                if (!this.destroyed) {
                    this.status = 'error';
                    console.error('App&Site Google Maps:', error);
                }
            }
        },

        selectLocation(index) {
            if (!Number.isInteger(index) || !this.locations[index]) {
                return;
            }

            this.activeIndex = index;
            this.updateMarkers();

            if (!this.map) {
                return;
            }

            const location = this.locations[index];
            this.map.panTo({ lat: location.lat, lng: location.lng });
        },

        updateMarkers() {
            this.markers.forEach((marker, index) => {
                const isActive = index === this.activeIndex;
                const pin = marker.element.querySelector('[data-map-marker-button]');

                if (!pin) {
                    return;
                }

                pin.classList.toggle('bg-primary-dark', !isActive);
                pin.classList.toggle('text-brand', !isActive);
                pin.classList.toggle('bg-brand', isActive);
                pin.classList.toggle('text-primary', isActive);
                pin.setAttribute('aria-pressed', String(isActive));
            });
        },

        zoom(step) {
            if (!this.map) {
                return;
            }

            const current = this.map.getZoom() || 13;
            this.map.setZoom(Math.min(19, Math.max(3, current + step)));
        },

        destroy() {
            this.destroyed = true;
            this.observer?.disconnect();
            this.observer = null;

            this.markers.forEach((marker) => marker.setMap(null));
            this.markers = [];

            if (this.map && window.google?.maps?.event) {
                google.maps.event.clearInstanceListeners(this.map);
            }

            this.map = null;
        },
    }));
});
