import Swiper from "swiper";

document.addEventListener('alpine:init', () => {
  Alpine.data('alpSwiper', (settings, thumb = null) => ({
    settings: settings,
    swiperContainer: null,
    swiper: null,
    thumb: thumb,
    swiper_thumb: null,

    init() {
      if(this.thumb) {
        this.swiper_thumb = new Swiper(this.$refs.thumb, this.thumb );
      }

      this.swiperContainer = this.$refs.swiper || this.$root;

      // add navigation
      if (this.settings.navigation) {
        this.settings.navigation = {
          prevEl: this.$refs.nav_left,
          nextEl: this.$refs.nav_right,
        }
      }

      if (this.thumb) {
        this.settings.thumbs = {
          swiper: this.swiper_thumb,
        }
      }

      this.swiper = new Swiper(this.swiperContainer, this.settings);

      // trick that is theoretically not suggested but sometimes works and helps
      // if(this.thumb) {
      //     this.swiper.controller.control = this.swiper_thumb;
      //     this.swiper_thumb.controller.control = this.swiper;
      // }
    }
  }))
})
