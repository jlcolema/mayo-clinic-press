class ThanksRows {
  constructor(element) {
    this.element = element;
    this.totalsRows = this.element.querySelectorAll('.totals-row');
    this.totalsRows.forEach(item => {
      item.innerHTML = item.innerHTML.replace('&nbsp;', '');
    });
  }
}

export default ThanksRows;
