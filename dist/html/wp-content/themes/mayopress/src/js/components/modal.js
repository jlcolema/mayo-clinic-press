import trapFocus from '../utils/focus-trap';
import { getCookie, setCookie } from '../utils';

class Modal {
  constructor(element) {
    this.element = element;
    this.config();
    this.mount();
  }

  config() {
    this.triggers = document.querySelectorAll(`[aria-controls="${this.element.getAttribute('id')}"]`);
    this.isOpen = false;
    this.selectedTrigger = null;
    this.preventScrollEl = this.getPreventScrollEl();
    this.showClass = 'modal--is-visible';
    this.showModal = this.showModal.bind(this);
    this.closeModal = this.closeModal.bind(this);
    this.createTrigger = this.createTrigger.bind(this);
    this.toShow = this.element.dataset.show;
    this.modalCookie = this.element.dataset.cookie;
  }

  mount() {
    if (this.modalCookie) {
      const hasCookie = getCookie(this.modalCookie);
      if (!hasCookie && this.toShow) {
        this.element.classList.add(this.showClass);
        setCookie(this.modalCookie, 'true', 36500);

        if (this.modalCookie === 'seenPersonalizedOptionsModal') {
          setCookie('seenPersonalizedTooltip', 'true', 0.25);

          // remove sticky footer
          if (document.querySelector('footer #email-capture')) document.querySelector('footer #email-capture').classList.remove('session--fixed');
          setCookie('stickyEmail', 'true', 0.25);
        }
      }
    } else if (this.toShow) {
      this.element.classList.add(this.showClass);
    }

    // Trap focus inside modal
    this.focusTrap = trapFocus(this.element, {
      toggleElement: this.selectedTrigger || this.triggers[0],
      onEscape: this.closeModal
    });

    this.bindEvents();
  }

  getToggleElement() {
    return this.selectedTrigger;
  }

  unmount() {
    this.focusTrap = null;
    this.unbindEvents();
  }

  bindEvents() {
    // open modal when clicking on trigger buttons
    this.triggers?.forEach(trigger => {
      trigger.addEventListener('click', this.createTrigger);
    });

    // listen to the modal/open event -> open modal without a trigger button
    this.element.addEventListener('theme/modal-open', this.showModal);

    // listen to the modal/close event -> close modal without a trigger button
    this.element.addEventListener('theme/modal-close', this.closeModal);

    // if modal is open by default -> initialize modal events
    if (this.element.classList.contains(this.showClass)) {
      this.isOpen = true;
      this.initModalEvents();
    }
  }

  unbindEvents() {
    this.triggers?.forEach(trigger => {
      trigger.removeEventListener('click', this.createTrigger);
    });

    this.element.removeEventListener('theme/modal-open', this.showModal);
    this.element.removeEventListener('theme/modal-close', this.closeModal);
  }

  createTrigger(event) {
    event.preventDefault();

    if (this.isOpen) {
      this.closeModal(event);
      return;
    }

    this.selectedTrigger = event.target;
    this.showModal(event);
    this.initModalEvents();
  }

  showModal(event) {
    if (event?.detail) this.selectedTrigger = event.detail;
    this.focusTrap.activate();

    this.element.classList.add(this.showClass);
    this.emitModalEvents('theme/modal-opened');

    // change the overflow of the preventScrollEl
    if (this.preventScrollEl) this.preventScrollEl.style.overflow = 'hidden';

    this.isOpen = true;

    this.initModalEvents();
  }

  closeModal(event) {
    if (event?.detail) this.selectedTrigger = event.detail;
    this.focusTrap.deactivate();

    if (!this.element.classList.contains(this.showClass)) return;
    this.element.classList.remove(this.showClass);

    // remove listeners
    this.cancelModalEvents();
    this.emitModalEvents('theme/modal-closed');
    // change the overflow of the preventScrollEl
    if (this.preventScrollEl) this.preventScrollEl.style.overflow = '';

    this.isOpen = false;
  }

  initModalEvents() {
    // add event listeners
    this.element.addEventListener('keydown', this);
    this.element.addEventListener('click', this);
  }

  cancelModalEvents() {
    // remove event listeners
    this.element.removeEventListener('keydown', this);
    this.element.removeEventListener('click', this);
  }

  emitModalEvents(eventName) {
    const event = new CustomEvent(eventName, { detail: this.selectedTrigger });
    this.element.dispatchEvent(event);
  }

  handleEvent(event) {
    switch (event.type) {
      case 'click': {
        this.initClick(event);
        break;
      }
      case 'keydown': {
        this.initKeyDown(event);
        break;
      }
      default: {
        break;
      }
    }
  }

  initKeyDown(event) {
    if ((event.key && event.key === 'Enter') && event.target.closest('.js-modal__close')) {
      event.preventDefault();
      this.closeModal(event); // close modal when pressing Enter on close button
    }
  }

  initClick(event) {
    // close modal when clicking on close button or modal bg layer
    if (!event.target.closest('.js-modal__close') && !event.target.classList.contains('js-modal')) return;
    event.preventDefault();
    this.closeModal();
  }

  getPreventScrollEl() {
    let scrollEl = false;
    const querySelector = this.element.getAttribute('data-modal-prevent-scroll');
    if (querySelector) scrollEl = document.querySelector(querySelector);
    return scrollEl;
  }
}

export default Modal;
