class AutoRenewConsent {
  constructor(element) {
    this.element = element;
    this.form = this.element.closest('form');
    this.consentInput = this.element.querySelector('#auto-renewal__consent--input');
    this.buyButton = this.element.querySelector('.single_add_to_cart_button');

    this.init();
  }

  init() {
    this.addListener();
  }

  addListener() {
    this.form.addEventListener('submit', event => {
      if (!this.consentInput.checked) {
        event.preventDefault();
        this.showTooltip();

        if (window.innerWidth < 1024) this.element.scrollIntoView({ block: 'nearest' });
      }
    });
  }

  showTooltip() {
    const tooltip = document.createElement('div');
    tooltip.classList.add('auto-renewal__consent--tooltip');
    tooltip.innerHTML = `Please confirm you\'ve read and agree to the terms and conditions before adding to cart.
      <button class="reset tooltip--close">
        <svg class="icon icon--close" aria-hidden="true" focusable="false">
          <use xlink:href="#close"></use>
        </svg>
      </button>`;

    const checkboxContainer = document.querySelector('.auto-renewal__consent');
    checkboxContainer.appendChild(tooltip);

    document.querySelector('body').addEventListener('click', e => {
      if (!e.target.closest('.auto-renewal__consent--tooltip')) {
        checkboxContainer.removeChild(tooltip);
      }
    });
    tooltip.querySelector('.tooltip--close').addEventListener('click', () => {
      checkboxContainer.removeChild(tooltip);
    });
  }
}

export default AutoRenewConsent;
