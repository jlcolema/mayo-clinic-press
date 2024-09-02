class EmailOptin {
  constructor(element) {
    this.element = element;
    this.formTarget = element.dataset.target;
    this.optinCheckbox = element.querySelector('#email-optin');
    this.billEmail = document.getElementById('billing_email');
    this.emailInput = document.querySelector(`.${this.formTarget} input[type="email"]`);
    this.emailSubmit = document.querySelector(`.${this.formTarget} input[type="submit"]`);

    this.addClick();
  }

  checkButton() {
    const proceedButton = document.querySelector('#place_order');
    const disabledProceeedButton = document.querySelector('#place_order[disabled]');
    let isDisabled = false;

    if (proceedButton === disabledProceeedButton) {
      isDisabled = true;
    }

    if (isDisabled) {
      return false;
    }
    return proceedButton;
  }

  optin() {
    const optin = this.optinCheckbox.checked;

    if (optin && this.billEmail.value.trim()) {
      this.emailInput.value = this.billEmail.value;
      this.emailSubmit.click();
    }
  }

  addClick() {
    let placeOrder = this.checkButton();

    if (placeOrder) {
      placeOrder.addEventListener('click', () => {
        this.optin();
      });
    } else {
      const paymentTarget = document.querySelector('.woo-checkout-bottom');
      const paymentObserver = new MutationObserver(() => {
        placeOrder = this.checkButton();
        if (placeOrder) {
          paymentObserver.disconnect();

          placeOrder.addEventListener('click', () => {
            this.optin();
          });
        }
      });
      paymentObserver.observe(paymentTarget, { subtree: true, childList: true, attributes: true });
    }
  }
}

export default EmailOptin;
