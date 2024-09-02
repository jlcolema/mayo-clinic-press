// Sale Price Formatting
class SalePriceFormat {
  constructor(element) {
    this.element = element;
    this.fullPrice = this.element.querySelectorAll('.item-sale del bdi');
    this.fullPriceDiv = this.element.querySelectorAll('.item-sale del');
    this.onSale = this.element.querySelectorAll('.item-sale ins bdi');
    if (this.onSale.length > 0) {
      this.initSale();
    }
  }

  initSale() {
    this.onSale.forEach((sale, index) => {
      const full = this.fullPrice[index];
      const saleP = parseFloat(sale.textContent.replace('$', ''));
      const fullP = parseFloat(full.textContent.replace('$', ''));
      let percent = ((fullP - saleP) / fullP) * 100;
      percent = (Math.floor(percent));
      const percentOff = document.createElement('span');
      percentOff.className = 'percent-off';
      percentOff.innerHTML = `${percent}% OFF`;
      this.fullPriceDiv[index].insertAdjacentElement('beforebegin', percentOff);
    });
  }
}

export default SalePriceFormat;
