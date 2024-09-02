import SnapSlider from '../blocks/post-carousel';

class FeaturedSlider extends SnapSlider {
  config() {
    super.config();

    this.options.imgRef = '.slide__image--container';

    this.timer = this.slider.querySelector('.slide__timer div');
    this.i = 0;
    this.sliderTitleLabel = 'FeaturedSlider';
  }

  stopTimer() {
    if (window.innerWidth >= 1024) {
      if (this.slideInterval) clearInterval(this.slideInterval);
    }
  }

  startTimer() {
    if (this.slideInterval) clearInterval(this.slideInterval);
    this.slideInterval = setInterval(this.setSlider.bind(this), 1000);
  }

  initEvents() {
    super.initEvents();

    this.slideInterval = setInterval(this.setSlider.bind(this), 1000);
    this.slider.addEventListener('mouseenter', this.stopTimer.bind(this));
    this.slider.addEventListener('mouseleave', this.startTimer.bind(this));
    this.slider.addEventListener('focusin', this.stopTimer.bind(this));
    this.slider.addEventListener('focusout', this.startTimer.bind(this));

    this.arrows.forEach(arrow => {
      arrow.addEventListener('click', this.resetTimer.bind(this));
    });

    this.pagination?.addEventListener('click', this.resetTimer.bind(this));
  }

  setSlider() {
    this.timer.style.width = `${this.i}%`;

    if (this.i >= 100) {
      const nextArrow = this.slider.querySelector('.slider__arrow--next');
      if (nextArrow.classList.contains('is-disabled')) {
        this.goTo(0);
      } else {
        const index = Math.min(this.items.length - 1, this.currentIndex + this.shownItems);
        this.goTo(index);
      }
      this.resetTimer();
    }

    this.i += 10;
  }

  resetTimer() {
    this.i = 0;
    this.timer.style.width = `${this.i}%`;
  }

  updateNavigation() {
    super.updateNavigation();

    this.timer = this.slider.querySelector('.slide__timer div');
    this.resetTimer();
  }

  updateAriaHiddenAttributes() {
    // do nothing in this subclass
  }
}

export default FeaturedSlider;
