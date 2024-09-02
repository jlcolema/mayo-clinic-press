import trapFocus from '../utils/focus-trap';

const { viewport } = window.theme;

class MobileMenu {
  constructor(element) {
    this.element = element;
    this.config();
    this.mount();
  }

  config() {
    this.toggleBtn = this.element.querySelector('.mobile-menu-btn');
    this.nav = this.element.querySelector(`#${this.toggleBtn.getAttribute('aria-controls')}` || 'nav');
    this.isOpen = false;
    this.closeBtn = this.nav.querySelector('.js-drawer__close');
    this.breakpoint = 'md';
    this.openClasses = {
      body: 'mobile-menu--is-open',
      mobileMenu: 'drawer--is-visible',
      toggleBtn: 'mobile-menu-btn--is-open',
    };

    // Bind events and save so they can be referenced
    this.toggleNav = this.toggleNav.bind(this);
    this.closeNav = this.closeNav.bind(this);
  }

  mount() {
    if (!this.toggleBtn || !this.nav) return;

    // Trap focus inside drawer
    this.focusTrap = trapFocus(this.nav, {
      toggleElement: this.toggleBtn,
      onEscape: this.toggleNav
    });

    this.bindEvents();
  }

  unmount() {
    this.unbindEvents();
    this.focusTrap = null;
  }

  bindEvents() {
    this.toggleBtn.addEventListener('click', this.toggleNav);
    this.closeBtn.addEventListener('click', this.closeNav);
    this.nav.addEventListener('click', this.closeNav);

    // Unset drawer options on viewport change
    viewport.on(this.breakpoint, this.closeNav);
  }

  unbindEvents() {
    this.toggleBtn.removeEventListener('click', this.toggleNav);
    this.closeBtn.removeEventListener('click', this.closeNav);
    this.nav.removeEventListener('click', this.closeNav);
    viewport.unsubscribe(this.closeNav);
  }

  toggleNav(bool) {
    this.isOpen = bool ?? !this.isOpen;

    // Set classes and attributes
    // this.toggleBtn.classList.toggle(this.openClasses.toggleBtn, this.isOpen);
    this.nav.classList.toggle(this.openClasses.mobileMenu, this.isOpen);
    document.body.classList.toggle(this.openClasses.body, this.isOpen);
    this.toggleBtn.setAttribute('aria-expanded', this.isOpen);
    this.nav.setAttribute('aria-hidden', !this.isOpen);

    // close all drawers
    if (!this.isOpen) {
      document.querySelectorAll('.drawer--is-visible').forEach(openDrawer => {
        openDrawer.classList.remove('drawer--is-visible');
      });
    }

    // Set focus trap
    if (this.isOpen) {
      this.focusTrap.activate();
    } else {
      this.focusTrap.deactivate();
    }
  }

  closeNav(event) {
    if ((event?.target !== this.closeBtn && event?.target.closest('.drawer__content'))) return;
    this.toggleNav(false);
  }
}

export default MobileMenu;
