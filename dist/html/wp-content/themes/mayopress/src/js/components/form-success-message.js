class FormSuccessMessage {
  constructor(element) {
    this.element = element;
    this.successMessageSelector = this.element.dataset.selector;
    this.successMessage = this.element.getAttribute('data-success-message') || '';
    this.hideSelectors = this.element.getAttribute('data-hide-selectors');
    this.hideForm();
  }

  hideForm() {
    document.addEventListener('wpcf7mailsent', e => {
      this.element.querySelector('form').classList.add('disable');
      if (this.hideSelectors
        || (this.successMessageSelector && document.querySelector(this.successMessageSelector))) {
        const successInterval = setInterval(() => {
          if (this.element.querySelector('form').classList.contains('sent')) {
            clearInterval(successInterval);
            this.element.querySelector('form').classList.remove('disable');
            if (this.hideSelectors) {
              document.querySelectorAll(this.hideSelectors).forEach(el => {
                el.style.display = 'none';
              });
            }
            if (this.successMessageSelector
              && document.querySelector(this.successMessageSelector)) {
              document.querySelector(this.successMessageSelector).textContent = this.successMessage;
            }

            const formTracking = e.target.closest('[data-component="form-tracking"]');
            if (formTracking.classList.contains('preorder__container')) {
              formTracking.classList.add('success');
            }
          }
        }, 250);
      }
    }, false);
  }
}

export default FormSuccessMessage;
