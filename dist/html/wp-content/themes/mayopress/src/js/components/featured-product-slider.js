import FeaturedSlider from './featured-slider';

class FeaturedProductSlider extends FeaturedSlider {
  config() {
    super.config();

    this.paginationPages = this.slider.querySelector('.slider__pagination--pages');
  }

  mount() {
    super.mount();

    this.removePaginationDots();
    this.addPagination();
  }

  addPagination() {
    this.paginationPages.innerHTML = `${(this.currentIndex + 1) / this.shownItems}/${this.items.length}`;
  }

  updateNavigation() {
    // Arrows
    if (this.arrows.length) {
      // Disable previous or next arrow if first/last item is in viewport
      const isPrevDisabled = this.currentVisible.includes(0);
      const isNextDisabled = this.currentVisible.includes(this.items.length - 1);

      this.arrows[0].classList.toggle(this.disabledCls, isPrevDisabled);
      this.arrows[0].setAttribute('tabindex', isPrevDisabled ? -1 : 0);
      this.arrows[2].classList.toggle(this.disabledCls, isPrevDisabled);
      this.arrows[1].classList.toggle(this.disabledCls, isNextDisabled);
      this.arrows[1].setAttribute('tabindex', isNextDisabled ? -1 : 0);
      this.arrows[3].classList.toggle(this.disabledCls, isNextDisabled);
    }

    this.paginationPages.innerHTML = `${(this.currentIndex + 1) / this.shownItems}/${this.items.length}`;
  }
}

export default FeaturedProductSlider;
