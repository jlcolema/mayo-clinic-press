class Accordion {
  constructor(element) {
    this.element = element;
    this.createAccount = this.element.querySelector('#createaccount');
    this.accountPass = this.element.querySelector('#account_password');

    this.init();
  }

  init() {
    if (!this.accountPass) return;

    this.accountPass.addEventListener('change', this.debounce(this.toggleCreate.bind(this), 500));
  }

  toggleCreate() {
    if (this.accountPass.value) {
      this.createAccount.checked = true;
    } else {
      this.createAccount.checked = false;
    }
  }

  debounce(func, delay) {
    let inDebounce;
    return () => {
      clearTimeout(inDebounce);
      inDebounce = setTimeout(() => {
        func();
      }, delay);
    };
  }
}

export default Accordion;
