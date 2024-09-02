class QuantityUpdate {
  constructor(element) {
    this.element = element;
    this.variant = this.element.dataset.variant;
    this.min = this.element.dataset.min;
    this.max = this.element.dataset.max;
    this.qtyInput = this.element.querySelector('input');
    this.minus = this.element.querySelector('.qty--minus');
    this.plus = this.element.querySelector('.qty--plus');

    if (this.qtyInput && this.minus && this.plus) {
      this.addEvents();
    }
  }

  addEvents() {
    this.minus.addEventListener('click', () => {
      const dataPrice = this.element.dataset.price;
      if (this.qtyInput.value > this.min) {
        const qtyPrice = document.querySelector('.qty--price');
        this.qtyInput.value -= 1;
        let updatedPrice = parseFloat(dataPrice * this.qtyInput.value).toFixed(2);

        if (this.variant) {
          updatedPrice = `$${updatedPrice} / year`;
        }

        if (qtyPrice) {
          qtyPrice.textContent = updatedPrice;
        }
      }
    });

    this.plus.addEventListener('click', () => {
      const dataPrice = this.element.dataset.price;
      if (this.max !== -1 || this.qtyInput.value < this.max) {
        const qtyPrice = document.querySelector('.qty--price');
        this.qtyInput.value = parseInt(this.qtyInput.value, 10) + 1;
        let updatedPrice = parseFloat(dataPrice * this.qtyInput.value).toFixed(2);

        if (this.variant) {
          updatedPrice = `$${updatedPrice} / year`;
        }

        if (qtyPrice) {
          qtyPrice.textContent = updatedPrice;
        }
      }
    });
  }
}

export default QuantityUpdate;
