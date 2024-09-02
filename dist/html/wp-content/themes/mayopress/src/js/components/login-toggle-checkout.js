class LoginSwitches {
  constructor(element) {
    this.element = element;
    this.showLogin = this.element.querySelector('.showlogin');

    this.setupEvents();
  }

  setupEvents() {
    if (!this.showLogin) return;
    this.showLogin.insertAdjacentHTML('beforeend', '<svg class="icon icon--sm icon--close" aria-hidden="true" focusable="false"><use xlink:href="#close"></use></svg>');
    this.showLogin.addEventListener('click', this.addToggle.bind(this));
  }

  addToggle() {
    if (this.element.classList.contains('show-login')) {
      this.element.classList.remove('show-login');
      this.showLogin.classList.add('btn');
    } else {
      this.element.classList.add('show-login');
      this.showLogin.classList.remove('btn');
    }
  }
}

export default LoginSwitches;
