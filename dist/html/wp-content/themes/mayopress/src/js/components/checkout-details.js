import getSfgShipping from '../utils/sfg-shipping';

class CheckoutDetails {
  constructor(element) {
    this.element = element;
    this.custSummary = this.element.querySelector('.customer-summary');
    this.form = this.element.querySelector('form.checkout');
    this.rightCol = this.element.querySelector('.woo-checkout-right');

    // shipping fields
    this.shipFirst = this.element.querySelector('#shipping_first_name');
    this.shipLast = this.element.querySelector('#shipping_last_name');
    this.shipCompanyField = this.element.querySelector('#shipping_company_field');
    this.shipCountry = this.element.querySelector('#shipping_country');
    this.shipAddress = this.element.querySelector('#shipping_address_1');
    this.shipAddress2Field = this.element.querySelector('#shipping_address_2_field');
    this.shipCity = this.element.querySelector('#shipping_city');
    this.shipState = this.element.querySelector('#shipping_state');
    this.shipZip = this.element.querySelector('#shipping_postcode');

    // billing fields
    this.billFirst = this.element.querySelector('#billing_first_name');
    this.billLast = this.element.querySelector('#billing_last_name');
    this.billCompanyField = this.element.querySelector('#billing_company_field');
    this.billCountry = this.element.querySelector('#billing_country');
    this.billAddress = this.element.querySelector('#billing_address_1');
    this.billAddress2Field = this.element.querySelector('#billing_address_2_field');
    this.billCity = this.element.querySelector('#billing_city');
    this.billState = this.element.querySelector('#billing_state');
    this.billZip = this.element.querySelector('#billing_postcode');
    this.billPhone = this.element.querySelector('#billing_phone');
    this.billEmail = this.element.querySelector('#billing_email');

    this.accountPass = this.element.querySelector('#account_password');
    this.addrArray = ['billing_address_1', 'billing_city', 'billing_postcode', 'billing_email', 'shipping_address_1', 'shipping_city', 'shipping_postcode'];

    // step variables
    this.currentStep = document.querySelector('.checkout__steps > .active');
    this.addressStep = document.querySelector('#stepAddress');
    this.methodStep = document.querySelector('#stepMethod');
    this.reviewStep = document.querySelector('#stepReview');
    this.paymentStep = document.querySelector('#stepPayment');
    this.btnsToAddress = this.element.querySelectorAll('.btn--to-shipping-address');
    this.btnsToShipMethod = this.element.querySelectorAll('.btn--to-shipping-method');
    this.btnsToReview = this.element.querySelectorAll('.btn--to-review');

    // billing variables
    this.billDiffShip = this.element.querySelector('#ship-to-different-address-checkbox');
    this.billForm = this.element.querySelector('.woo-billing');

    this.shipOptions = this.element.querySelector('.shipping-options');

    this.changeHTML();

    this.billCompany = this.element.querySelector('#billing_company');
    this.shipCompany = this.element.querySelector('#shipping_company');
    this.billAddress2 = this.element.querySelector('#billing_address_2');
    this.shipAddress2 = this.element.querySelector('#shipping_address_2');

    this.updateShipping();

    this.setHeight();

    // step functions
    this.stepEvents();

    // bill form event
    this.billFormClick();
  }

  changeHTML() {
    if (this.billCompanyField) {
      this.billCompanyField.innerHTML = `<details>
          <summary>Company Name (optional)</summary>
          <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text" name="billing_company" id="billing_company" placeholder="" value="" data-required="0" autocomplete="organization">
          </span>
        </details>`;
    }

    if (this.billAddress2Field) {
      this.billAddress2Field.innerHTML = `<details>
          <summary>Add Address Line 2 (optional)</summary>
          <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text " name="billing_address_2" id="billing_address_2" placeholder="Apartment, suite, unit, etc. (optional)" value="" data-required="0" autocomplete="address-line2">
          </span>
        </details>`;
    }

    if (this.shipCompanyField) {
      this.shipCompanyField.innerHTML = `<details>
          <summary>Company Name (optional)</summary>
          <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text " name="shipping_company" id="shipping_company" placeholder="" value="" data-required="0" autocomplete="organization">
          </span>
        </details>`;
    }

    if (this.shipAddress2Field) {
      this.shipAddress2Field.innerHTML = `<details>
          <summary>Add Address Line 2 (optional)</summary>
          <span class="woocommerce-input-wrapper">
            <input type="text" class="input-text" name="shipping_address_2" id="shipping_address_2" placeholder="Apartment, suite, unit, etc. (optional)" value="" data-required="0" autocomplete="address-line2">
          </span>
        </details>`;
    }
  }

  updateShipping() {
    for (let i = 0; i < this.addrArray.length; i++) {
      const targetField = this.element.querySelector(`#${this.addrArray[i]}`);

      if (targetField) {
        targetField.addEventListener('focus', () => {
          const placeOrderButton = document.querySelector('#place_order');
          const placeOrderHolderButton = document.querySelector('#place_order_holder');
          if (placeOrderButton) {
            placeOrderButton.setAttribute('disabled', '');
          }
          if (placeOrderHolderButton) {
            placeOrderHolderButton.setAttribute('disabled', '');
          }
        });
      }
    }
  }

  setHeight() {
    if (this.form && this.rightCol) {
      this.form.setAttribute('style', `min-height: calc(${this.rightCol.offsetHeight}px + var(--space-base));`);
    }
  }

  checkValid(arrToCheck) {
    if (document.querySelector('.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout')) document.querySelector('.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout').remove();

    let hasError = false;
    let errorMarkup = '';
    for (let i = 0; i < arrToCheck.length; i++) {
      if (!arrToCheck[i].value) {
        if (!hasError) {
          errorMarkup = '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout"><ul class="woocommerce-error" role="alert">';
          hasError = true;
        }

        errorMarkup += `<li data-id="${arrToCheck[i].id}"><strong>${arrToCheck[i].labels[0]?.textContent.replace(' *', '').replace('*', '')}</strong> is a required field.</li>`;
      }
    }

    if (hasError) {
      errorMarkup += '</ul></div>';
      this.form.insertAdjacentHTML('afterbegin', errorMarkup);
      this.form.scrollIntoView({ behavior: 'smooth' });

      return false;
    }

    return true;
  }

  setActive(willBeActive) {
    this.currentStep.classList.remove('active');
    willBeActive.classList.add('active');
    this.currentStep = willBeActive;
  }

  stepEvents() {
    this.btnsToAddress.forEach(toAddress => {
      toAddress.addEventListener('click', () => {
        // hide shipping options, will rerender after sfg api call
        if (!this.shipOptions.classList.contains('is-invisible')) this.shipOptions.classList.add('is-invisible');

        this.setActive(this.addressStep);

        if (toAddress.classList.contains('to--billing')) {
          if (this.billDiffShip && !this.billDiffShip.checked) this.billDiffShip.checked = true;
          this.billForm.scrollIntoView({ behavior: 'smooth' });
        }
      });
    });

    this.btnsToShipMethod.forEach(toMethod => {
      toMethod.addEventListener('click', () => {
        if (toMethod.classList.contains('change')) {
          this.setActive(this.methodStep);
        } else if (this.billDiffShip.checked) {
          const reqFields = [
            this.shipFirst,
            this.shipLast,
            this.shipCountry,
            this.shipAddress,
            this.shipCity,
            this.shipState,
            this.shipZip,
            this.billFirst,
            this.billLast,
            this.billCountry,
            this.billAddress,
            this.billCity,
            this.billState,
            this.billZip,
            this.billPhone,
            this.billEmail
          ];

          if (this.accountPass?.closest('#account_password_field')?.querySelector('.required')) {
            reqFields.push(this.accountPass);
          }

          if (this.checkValid(reqFields)) {
            this.custSummary.querySelector('.customer__name').textContent = `${this.billFirst.value} ${this.billLast.value}`;
            this.custSummary.querySelector('.customer__email').textContent = `${this.billEmail.value}`;
            this.custSummary.querySelector('.customer__address').textContent = `${this.shipAddress.value}, ${this.shipAddress2.value ? `${this.shipAddress2.value}, ` : ''} ${this.shipCity.value} ${this.shipState.value} ${this.shipZip.value}`;
            this.custSummary.querySelector('.customer__billing').textContent = `${this.billAddress.value}, ${this.billAddress2.value ? `${this.billAddress2.value}, ` : ''} ${this.billCity.value} ${this.billState.value} ${this.billZip.value}`;
            if (this.custSummary.querySelector('.customer__create')) {
              this.custSummary.querySelector('.customer__create').textContent = this.accountPass && this.accountPass.value ? 'Yes' : 'No';
            }

            this.setActive(this.methodStep);
            getSfgShipping();
          }
        } else {
          const reqFields = [
            this.shipFirst,
            this.shipLast,
            this.shipCountry,
            this.shipAddress,
            this.shipCity,
            this.shipState,
            this.shipZip,
            this.billPhone,
            this.billEmail
          ];

          if (this.accountPass?.closest('#account_password_field')?.querySelector('.required')) {
            reqFields.push(this.accountPass);
          }

          if (this.checkValid(reqFields)) {
            this.setBilling();

            this.custSummary.querySelector('.customer__name').textContent = `${this.shipFirst.value} ${this.shipLast.value}`;
            this.custSummary.querySelector('.customer__email').textContent = `${this.billEmail.value}`;
            this.custSummary.querySelector('.customer__address').textContent = `${this.shipAddress.value}, ${this.shipAddress2.value ? `${this.shipAddress2.value}, ` : ''} ${this.shipCity.value} ${this.shipState.value} ${this.shipZip.value}`;
            this.custSummary.querySelector('.customer__billing').textContent = `${this.shipAddress.value}, ${this.shipAddress2.value ? `${this.shipAddress2.value}, ` : ''} ${this.shipCity.value} ${this.shipState.value} ${this.shipZip.value}`;
            if (this.custSummary.querySelector('.customer__create')) {
              this.custSummary.querySelector('.customer__create').textContent = this.accountPass && this.accountPass.value ? 'Yes' : 'No';
            }

            this.setActive(this.methodStep);
            getSfgShipping();
          }
        }
      });
    });

    this.btnsToReview.forEach(toReview => {
      toReview.addEventListener('click', () => {
        if (!toReview.classList.contains('change')) {
          const selectedShipping = document.querySelector('.shipping-options input:checked');
          if (selectedShipping && selectedShipping.nextElementSibling) {
            const duplicateHtml = selectedShipping.nextElementSibling.outerHTML;
            this.custSummary.querySelector('.customer__method').innerHTML = duplicateHtml;
          } else {
            this.custSummary.querySelector('.customer__method').innerHTML = '<div class="shipping__method--text"><p><p>BPM</p><p>7-10 business days (FREE)</p></div>';
          }
        }
        this.setActive(this.reviewStep);
      });
    });
  }

  setBilling() {
    this.billFirst.value = this.shipFirst.value;
    this.billLast.value = this.shipLast.value;
    this.billCompanyField.value = this.shipCompanyField.value;
    this.billCountry.value = this.shipCountry.value;
    this.billAddress.value = this.shipAddress.value;
    this.billAddress2Field.value = this.shipAddress2Field.value;
    this.billCity.value = this.shipCity.value;
    this.billState.value = this.shipState.value;
    this.billZip.value = this.shipZip.value;
  }

  billFormClick() {
    this.billDiffShip.addEventListener('change', () => {
      if (this.billDiffShip.checked) {
        this.billForm.scrollIntoView({ behavior: 'smooth' });
      }
    });
  }
}

export default CheckoutDetails;
