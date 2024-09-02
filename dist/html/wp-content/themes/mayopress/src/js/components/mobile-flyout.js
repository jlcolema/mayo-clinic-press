class MobileFlyout {
  constructor(element) {
    this.element = element;
    this.triggers = this.element.querySelectorAll('.flyout__button--open');
    this.filterTotal = this.element.querySelector('.filters--total');
    this.classToOpen = 'flyout--open';
    this.selctedFacetsContainer = document.querySelector('.facets--selected');

    this.addOpenEvents();
    this.observeChanges();
  }

  addOpenEvents() {
    if (this.triggers) {
      this.triggers.forEach(trigger => {
        const containerClass = trigger.dataset.container;
        const targetContainer = document.querySelector(`.${containerClass}`);

        if (targetContainer) {
          const closeTriggers = targetContainer.querySelectorAll('.flyout__button--close');

          trigger.addEventListener('click', () => {
            targetContainer.classList.add(this.classToOpen);
          });

          this.addCloseEvents(targetContainer, closeTriggers);
        }
      });
    }
  }

  addCloseEvents(targetContainer, closeTriggers) {
    if (targetContainer && closeTriggers) {
      closeTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
          targetContainer.classList.remove(this.classToOpen);
        });
      });
    }
  }

  observeChanges() {
    if (this.selctedFacetsContainer && this.filterTotal) {
      const config = { subtree: true, childList: true };

      const updateTotal = () => {
        const total = this.selctedFacetsContainer.querySelectorAll('.facetwp-selection-value').length;
        if (total) {
          this.filterTotal.textContent = `(${total})`;
        } else {
          this.filterTotal.textContent = '';
        }
      };

      const observer = new MutationObserver(updateTotal);
      observer.observe(this.selctedFacetsContainer, config);
      updateTotal();
    }
  }
}

export default MobileFlyout;
