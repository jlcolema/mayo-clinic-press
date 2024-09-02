class ShopPromo {
  constructor(element) {
    this.element = element;
    this.links = this.element.querySelectorAll('a');

    if (this.links.length === 1) {
      this.makeCardLinkable();
    }
  }

  makeCardLinkable() {
    this.element.style.cursor = 'pointer';
    this.element.addEventListener('click', () => {
      this.links[0].click();
    });
  }
}

export default ShopPromo;
