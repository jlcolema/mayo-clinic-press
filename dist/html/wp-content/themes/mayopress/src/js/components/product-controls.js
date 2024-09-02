import { throttle } from '../utils';

const { viewport } = window.theme;

class ProductControls {
  constructor(element) {
    this.element = element;
    this.primaryImage = this.element.querySelector('.primary-slider .splide__slide');
    this.priceContainers = this.element.querySelectorAll('.price__container .price__single');
    this.atcButtons = this.element.querySelector('.product__buttons--container');

    this.productGallery();
    this.freeShipping();
    this.addImageAlt();

    if (this.atcButtons) {
      const stickyEvents = ['scroll', 'resize'];
      stickyEvents.forEach(trigger => {
        window.addEventListener(trigger, throttle(this.mobileStickyButtons.bind(this)));
      });
    }
  }

  productGallery() {
    if (this.primaryImage) {
      const sliderHeight = `${this.primaryImage.offsetHeight}px`;
      const galleryStyle = document.createElement('style');

      galleryStyle.innerHTML = `@media(min-width:1024px){#secondary-slider,#secondary-slider-track{height:${sliderHeight}!important}}`;
      this.element.prepend(galleryStyle);

      this.replaceZoomIcon();
    }
  }

  freeShipping() {
    if (this.priceContainers.length) {
      this.priceContainers.forEach(priceContainer => {
        const salePriceElement = priceContainer.querySelector('.price ins .woocommerce-Price-amount');
        const regularPriceElement = priceContainer.querySelector('.price .woocommerce-Price-amount:not(ins .woocommerce-Price-amount)');
        let price = 0;

        if (salePriceElement) {
          price = parseFloat(salePriceElement.textContent.replace('$', ''));
        } else if (regularPriceElement) {
          price = parseFloat(regularPriceElement.textContent.replace('$', ''));
        }

        if (price >= 45) {
          priceContainer.insertAdjacentHTML('beforeend', '<p class="price__free-shipping">+ FREE SHIPPING!</p>');
        }
      });
    }
  }

  replaceZoomIcon() {
    const icons = this.element.querySelectorAll('.fme_pgifw_right_bottom');

    if (icons.length) {
      icons.forEach(icon => {
        icon.innerHTML = '<svg class="icon icon--search" aria-hidden="true" focusable="false"><use xlink:href="#search"></use></svg>';
      });
    }
  }

  mobileStickyButtons() {
    if (!viewport.is('md')) {
      const isAtcVisible = document.querySelector('.product').getBoundingClientRect().bottom > 0;
      if (!isAtcVisible) {
        this.atcButtons.classList.add('fixed');
      } else {
        this.atcButtons.classList.remove('fixed');
      }
    } else {
      this.atcButtons.classList.remove('fixed');
    }
  }

  addImageAlt() {
    const allImages = document.querySelectorAll('img');
    if (allImages.length) {
      allImages.forEach(imag => {
        imag.setAttribute('alt', imag.alt);
      });
    }
  }
}

export default ProductControls;
