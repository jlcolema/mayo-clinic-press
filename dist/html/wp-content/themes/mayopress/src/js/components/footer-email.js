import { throttle, getCookie, setCookie } from '../utils';

class StickyEmailCapture {
  constructor(element) {
    this.element = element;
    this.footerEmail = this.element.querySelector('#email-capture');
    this.stickyClose = this.footerEmail?.querySelector('.icon--close');
    this.emailCookie = getCookie('stickyEmail');

    this.init();
  }

  init() {
    if (!this.emailCookie && this.footerEmail) {
      this.footerEmail.classList.add('session--fixed');
      this.closeSticky();

      this.footerEmail
        .querySelector('.wpcf7-form')
        .addEventListener('wpcf7mailsent', this.stickyFormSubmit.bind(this));
      document.addEventListener(
        'scroll',
        throttle(this.scrollEvent.bind(this))
      );
    }
  }

  closeSticky() {
    if (!this.stickyClose) return;

    this.stickyClose.addEventListener('click', () => {
      setCookie('stickyEmail', 'true', 2);
      this.emailCookie = true;
      this.footerEmail.classList.remove('session--fixed');
    });
  }

  stickyFormSubmit() {
    setTimeout(() => {
      setCookie('stickyEmail', 'true', 36500);
      this.emailCookie = true;
      this.footerEmail.classList.remove('session--fixed');
    }, 5000);
  }

  scrollEvent() {
    this.emailCookie = getCookie('stickyEmail');
    if (!this.emailCookie) {
      const rect = this.element.getBoundingClientRect();
      if (
        rect.top
        <= (window.innerHeight || document.documentElement.clientHeight)
      ) {
        this.footerEmail.classList.remove('session--fixed');
      } else {
        this.footerEmail.classList.add('session--fixed');
      }
    }
  }
}

export default StickyEmailCapture;
