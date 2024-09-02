import SnapSlider from '../blocks/post-carousel';

class SnapSliderAlt extends SnapSlider {
  // default behavior did not update arrows correctly
  // modified to do so in an inline header/carousel fashion
  updateNavigation() {
    super.updateNavigation();

    const containerLeft = this.slider.getBoundingClientRect().left;
    const containerRight = this.slider.getBoundingClientRect().right;
    const firstSlide = this.slider.querySelector('.slider__item');
    const lastSlide = this.slider.querySelectorAll('.slider__item')[this.slider.querySelectorAll('.slider__item').length - 1];

    setTimeout(() => {
      const isPrevDisabled = firstSlide.getBoundingClientRect().left >= containerLeft;
      const isNextDisabled = lastSlide.getBoundingClientRect().right <= containerRight + 5;

      this.arrows[0].classList.toggle(this.disabledCls, isPrevDisabled);
      this.arrows[0].setAttribute('tabindex', isPrevDisabled ? -1 : 0);
      this.arrows[1].classList.toggle(this.disabledCls, isNextDisabled);
      this.arrows[1].setAttribute('tabindex', isNextDisabled ? -1 : 0);
    }, 500);
  }
}

export default SnapSliderAlt;
