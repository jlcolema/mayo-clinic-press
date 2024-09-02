import { setAttributes, removeAttributes } from '../utils';

class Accordion {
  constructor(element) {
    this.element = element;
    this.hiddenElement = this.element.querySelector('.hide-below');
    this.openHeight = this.element.querySelector('.hidden-height')?.offsetHeight;
    this.toggle = this.element.querySelector('.additional-resources__toggle');

    if (this.hiddenElement && this.openHeight && this.toggle) {
      this.setToggle();
    }
  }

  setToggle() {
    if (!this.hiddenElement || !this.openHeight || !this.toggle) return;

    this.toggle.addEventListener('click', () => {
      if (this.toggle.getAttribute('aria-expanded') === 'true') {
        setAttributes(this.toggle, { 'aria-expanded': false });
        setAttributes(this.hiddenElement, { style: '--height: 0' });
      } else {
        setAttributes(this.toggle, { 'aria-expanded': true });
        setAttributes(this.hiddenElement, { style: `--height: ${this.openHeight}px` });
      }
    });
  }
}

export default Accordion;
