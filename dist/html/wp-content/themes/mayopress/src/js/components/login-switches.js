class LoginSwitches {
  constructor(element) {
    this.element = element;
    this.signInBut = document.querySelector('.signin-btn');
    this.registerBut = document.querySelector('button.register-btn');
    this.setupEvents();
  }

  setupEvents() {
    if (this.signInBut) {
      this.signInBut.addEventListener('click', () => {
        this.element.classList.remove('register');
      });
    }

    if (this.registerBut) {
      this.registerBut.addEventListener('click', () => {
        this.element.classList.add('register');

        const loginHeading = document.querySelector('.form-login-heading h2');
        if (loginHeading) loginHeading.textContent = 'Create a New Account';

        const wooNoticeWrapper = document.querySelector('.woocommerce-notices-wrapper');
        if (wooNoticeWrapper) wooNoticeWrapper.innerHTML = '';
      });
    }
  }
}

export default LoginSwitches;
