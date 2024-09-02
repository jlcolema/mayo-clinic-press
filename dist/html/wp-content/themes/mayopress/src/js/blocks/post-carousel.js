export default class SnapSlider {
  constructor(element) {
    this.slider = element;
    this.config();
    this.mount();

    if (!window.snapSliderInstances) {
      window.snapSliderInstances = new Map();
    }

    window.snapSliderInstances.set(element, this);
  }

  config() {
    this.options = {
      initial: 0,
      track: '.slider__track',
      arrows: '.slider__arrow',
      pagination: '.slider__pagination',
      numItemsProp: '--items',
      disabledCls: 'is-disabled',
      currentCls: 'is-current',
      items: '.slider__item',
      imgRef: '.post-card__image',
      filters: '.btn--filter',
      off: null,
      ...JSON.parse(this.slider.getAttribute('data-options') || '{}')
    };

    // Elements
    this.track = this.slider.querySelector(this.options.track);
    this.items = [...this.track.querySelectorAll(this.options.items)];
    this.activeItems = this.items;
    this.arrows = [...this.slider.querySelectorAll(this.options.arrows)];
    this.filters = [...this.slider.parentElement.querySelectorAll(this.options.filters)];
    this.pagination = this.slider.querySelector(this.options.pagination);
    this.paginationDots = null;

    // Variables
    this.currentIndex = this.options.initial;
    this.currentVisible = [];
    this.shownItems = Number(getComputedStyle(this.track)
      .getPropertyValue(this.options.numItemsProp)) || 1;
    this.disabledCls = this.options.disabledCls;
    this.currentCls = this.options.currentCls;

    // Bind functions to class
    this.handleItemFocus = this.handleItemFocus.bind(this);
    this.handleArrowNav = this.handleArrowNav.bind(this);
    this.handleFilter = this.handleFilter.bind(this);
    this.handlePaginationNav = this.handlePaginationNav.bind(this);

    // Create intersection observer
    this.scrollObserver = new IntersectionObserver(
      this.handleScroll.bind(this),
      { root: this.slider, threshold: 0.95 }
    );

    // Create resize observer
    this.resizeObserver = new ResizeObserver(this.handleResize.bind(this));

    // get slider title, remove special characters, and convert to pascal case
    if (this.slider.previousElementSibling && this.slider.previousElementSibling.tagName === 'HEADER') {
      this.sliderTitle = this.slider.previousElementSibling.querySelector('h2').textContent;
      this.sliderTitle = this.sliderTitle.replace(/[^A-Za-z0-9 ]/g, '');
      const words = this.sliderTitle.split(' ');

      this.sliderTitleLabel = words.map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join('').trim();
    }
  }

  mount() {
    this.initEvents();
    this.initObservers();
    this.imgOffset();
    this.addPaginationDots();
    this.goTo(this.currentIndex);
  }

  unmount() {
    this.cancelEvents();
    this.cancelObservers();
    this.removePaginationDots();
  }

  initEvents() {
    this.activeItems?.forEach(item => item.addEventListener('focusin', this.handleItemFocus));
    this.arrows?.forEach(arrow => arrow.addEventListener('click', this.handleArrowNav));
    this.filters?.forEach(filter => filter.addEventListener('click', this.handleFilter));
    this.pagination?.addEventListener('click', this.handlePaginationNav);
  }

  cancelEvents() {
    this.items?.forEach(item => item.removeEventListener('focusin', this.handleItemFocus));
    this.arrows?.forEach(arrow => arrow.removeEventListener('click', this.handleArrowNav));
    this.filters?.forEach(filter => filter.removeEventListener('click', this.handleFilter));
    this.pagination?.removeEventListener('click', this.handlePaginationNav);
  }

  initObservers() {
    this.activeItems.forEach(item => this.scrollObserver.observe(item));
    this.resizeObserver.observe(this.slider);
  }

  cancelObservers() {
    this.items.forEach(item => this.scrollObserver.unobserve(item));
    this.resizeObserver.unobserve(this.slider);
  }

  addPaginationDots() {
    this.paginationDots = [];

    this.activeItems.forEach((_, index) => {
      const dot = document.createElement('button');
      dot.classList.add('slider__pagination-item');

      const label = document.createElement('span');
      label.id = `labelFor${this.sliderTitleLabel}Dot${index + 1}`;
      label.textContent = `Slide ${index + 1} for ${this.sliderTitle} slider`;
      label.classList.add('sr-only');

      dot.setAttribute('aria-labelledby', label.id);

      this.paginationDots.push(dot);
      this.pagination.appendChild(dot);
      this.pagination.appendChild(label);
    });

    this.updateAriaHiddenAttributes();
  }

  updateAriaHiddenAttributes() {
    const mediaQueryList = window.matchMedia('(max-width: 63.999rem)');
    const isMobileView = mediaQueryList.matches;

    this.paginationDots.forEach(dot => {
      if (isMobileView) {
        dot.setAttribute('aria-hidden', 'false');
        dot.removeAttribute('tabindex');
      } else {
        dot.setAttribute('aria-hidden', 'true');
        dot.setAttribute('tabindex', '-1');
      }
    });
  }

  removePaginationDots() {
    this.pagination.innerHTML = '';
  }

  imgOffset() {
    const img = this.activeItems[0].querySelector(this.options.imgRef);
    if (!img) return;
    this.slider.style.setProperty('--img-offset', `${img.offsetHeight}px`);
  }

  goTo(index) {
    if (this.currentIndex !== index) {
      this.currentIndex = index;
      this.track.scroll({ top: 0, left: this.activeItems[index].offsetLeft, behavior: 'smooth' });
    }

    this.updateNavigation();
  }

  handleItemFocus(event) {
    const index = this.activeItems.indexOf(event.target);
    this.goTo(index);
  }

  handleArrowNav(event) {
    event.preventDefault();
    const button = this.arrows.indexOf(event.target.closest('button')) % 2 === 0 ? 'prev' : 'next';
    const index = button === 'prev'
      ? Math.max(0, this.currentIndex - this.shownItems)
      : Math.min(this.activeItems.length - 1, this.currentIndex + this.shownItems);

    this.goTo(index);
  }

  handleFilter(event) {
    event.preventDefault();
    const button = event.target.closest('button');
    if (!button) return;

    if (button.classList.contains('active')) {
      button.classList.remove('active');
      this.items.forEach(item => {
        item.classList.remove('unfiltered');
      });
      this.activeItems = this.items;
    } else {
      if (this.slider.parentElement.querySelector('.active')) this.slider.parentElement.querySelector('.active').classList.remove('active');
      button.classList.add('active');
      const getFilter = button.textContent.trim();
      this.activeItems = [];
      this.items.forEach(item => {
        const terms = item.dataset.terms?.split(',');
        if (terms.includes(getFilter)) {
          item.classList.remove('unfiltered');
          this.activeItems.push(item);
        } else {
          item.classList.add('unfiltered');
        }
      });
    }
    this.unmount();
    this.mount();
  }

  handlePaginationNav(event) {
    event.preventDefault();
    if (event.target.tagName !== 'BUTTON') return;
    const index = this.paginationDots.indexOf(event.target);
    this.goTo(index);
  }

  handleScroll(entries) {
    entries.forEach(entry => {
      // Find index of current entry in our items array
      const index = this.activeItems.indexOf(entry.target);

      if (!entry.isIntersecting) {
        // Remove from array if no longer intersecting
        this.currentVisible = this.currentVisible.filter(item => item !== index);
      } else if (!this.currentVisible.includes(index)) {
        // If it's visible and not already in the array, add to the currentVisible array
        this.currentVisible.push(index);
      }
    });

    // We know we're in the middle of a scroll if the visible length
    // is not the same as the number of items we show
    if (this.currentVisible.length !== this.shownItems) return;

    // Keep it in order
    this.currentVisible.sort((a, b) => a - b);

    // Set the current index to the highest number
    [this.currentIndex] = this.currentVisible;
    this.updateNavigation();
  }

  handleResize() {
    this.shownItems = Number(getComputedStyle(this.track)
      .getPropertyValue(this.options.numItemsProp));
    this.imgOffset();
    this.track.scroll({ top: 0, left: this.activeItems[this.currentIndex].offsetLeft, behavior: 'smooth' });

    this.updateAriaHiddenAttributes();
  }

  updateNavigation() {
    // Arrows
    if (this.arrows.length) {
      // Disable previous or next arrow if first/last item is in viewport
      const isPrevDisabled = this.currentVisible.includes(0);
      const isNextDisabled = this.currentVisible.includes(this.activeItems.length - 1);

      this.arrows[0].classList.toggle(this.disabledCls, isPrevDisabled);
      this.arrows[0].setAttribute('tabindex', isPrevDisabled ? -1 : 0);
      this.arrows[1].classList.toggle(this.disabledCls, isNextDisabled);
      this.arrows[1].setAttribute('tabindex', isNextDisabled ? -1 : 0);
    }

    // Pagination
    this.paginationDots.forEach((el, index) => {
      el.classList.toggle(this.currentCls, this.currentIndex === index);
    });
  }
}
