class MegaMenu {
  constructor(element) {
    this.element = element;
    this.config();
    this.mount();
  }

  config() {
    this.menuItems = this.element.querySelectorAll('.menu__item--has-children .menu__link');
    this.menuButtons = null;
    this.currentMenuItem = null;
    this.closeOpenMenu = this.closeOpenMenu.bind(this);
    this.closeOnEsc = this.closeOnEsc.bind(this);
    this.toggleOnMenuClick = this.toggleOnMenuClick.bind(this);
    this.hoverTimeout = null;
  }

  mount() {
    this.initEvents();
    this.convertLinksToButtons();
    this.setUpAria();
    this.topics();
  }

  unmount() {
    this.cancelEvents();
    this.convertButtonsToLinks();
  }

  initEvents() {
    document.addEventListener('click', this.closeOpenMenu);
    this.element.addEventListener('keyup', this.closeOnEsc);
  }

  cancelEvents() {
    document.removeEventListener('click', this.closeOpenMenu);
    this.element.addEventListener('keyup', this.closeOnEsc);
  }

  convertLinksToButtons() {
    this.menuItems.forEach(item => {
      const linkHTML = item.innerHTML;
      const button = document.createElement('button');

      button.innerHTML = linkHTML.trim();
      button.classList.add('menu__link');
      item.replaceWith(button);
    });

    this.menuButtons = this.element.querySelectorAll('button.menu__link');
    this.menuButtons.forEach(item => item.addEventListener('click', this.toggleOnMenuClick));
    this.menuButtons.forEach(item => item.closest('.menu__item').addEventListener('mouseenter', this.toggleOnMenuClick));
    this.menuButtons.forEach(item => item.closest('.menu__item').addEventListener('mouseleave', this.toggleOnMenuClick));
  }

  convertButtonsToLinks() {
    this.menuButtons.forEach((item, index) => item.replaceWith(this.menuItems[index]));
  }

  setUpAria() {
    this.menuButtons.forEach(item => {
      const submenuId = item.getAttribute('id');
      const submenu = item.nextElementSibling;
      if (!submenu) return;

      let id;

      if (submenuId === null) {
        id = `${item.textContent.trim().replace(/\s+/g, '-').toLowerCase()}-submenu`;
      } else {
        id = `${submenuId}-submenu`;
      }

      // set button ARIA
      item.setAttribute('aria-controls', id);
      item.setAttribute('aria-expanded', false);

      // set submenu ARIA
      submenu.setAttribute('id', id);
      submenu.setAttribute('aria-hidden', true);
    });
  }

  toggleOnMenuClick(event) {
    const button = event.target.closest('button.menu__link') || event.target.closest('.menu__item').querySelector('button.menu__link');
    const isMenuOpen = button.getAttribute('aria-expanded') === 'true';

    // close open menu if there is one
    if (this.currentMenuItem && button && button !== this.currentMenuItem) {
      switch (event.type) {
        case 'mouseenter':
          if (this.hoverTimeout) clearTimeout(this.hoverTimeout);
          if (!isMenuOpen) {
            this.toggleSubmenu(this.currentMenuItem);
          }
          break;
        case 'mouseleave':
          if (isMenuOpen) {
            this.hoverTimeout = setTimeout(() => {
              this.toggleSubmenu(this.currentMenuItem);
              this.hoverTimeout = null;
            }, 500);
          }
          break;
        default:
          this.toggleSubmenu(this.currentMenuItem);
      }
    }

    switch (event.type) {
      case 'mouseenter':
        if (this.hoverTimeout) clearTimeout(this.hoverTimeout);
        if (!isMenuOpen) {
          this.toggleSubmenu(button);
        }
        break;
      case 'mouseleave':
        if (isMenuOpen) {
          this.hoverTimeout = setTimeout(() => {
            this.toggleSubmenu(button);
            this.hoverTimeout = null;
          }, 500);
        }
        break;
      default:
        this.toggleSubmenu(button);
    }
  }

  toggleSubmenu(button) {
    const submenu = button.nextElementSibling;

    if (button.getAttribute('aria-expanded') === 'true') {
      button.setAttribute('aria-expanded', false);
      submenu.setAttribute('aria-hidden', true);
      this.currentMenuItem = null;
    } else {
      button.setAttribute('aria-expanded', true);
      submenu.setAttribute('aria-hidden', false);
      this.lastFocusedButton = document.activeElement;
      this.currentMenuItem = button;
    }
  }

  closeOpenMenu(event) {
    if (this.currentMenuItem && !event.target.closest(`#${this.element.id}`)) {
      this.toggleSubmenu(this.currentMenuItem);
    }
  }

  closeOnEsc(event) {
    if (event.keyCode === 27 && this.currentMenuItem.getAttribute('aria-expanded') === 'true') {
      this.toggleSubmenu(this.currentMenuItem);
      if (this.lastFocusedButton) {
        this.lastFocusedButton.focus();
      }
    }
  }

  topics() {
    const topicsMegaMenu = this.element.querySelector('#more-topics-submenu.submenu--mega-menu');
    if (!topicsMegaMenu) return;

    const megaMenuLinks = topicsMegaMenu.querySelectorAll('.widget li');
    const megaMenuImages = topicsMegaMenu.querySelectorAll('[data-imgId]');

    if (megaMenuLinks.length && megaMenuImages.length) {
      megaMenuImages[0].classList.remove('is-hidden');

      megaMenuLinks.forEach(menuLink => {
        menuLink.addEventListener('mouseenter', () => {
          const imgId = menuLink.classList[0];
          const image = topicsMegaMenu.querySelector(`[data-imgId="${imgId}"]`);

          if (image) {
            megaMenuImages[0].classList.add('is-hidden');
            image.classList.remove('is-hidden');
          }
        });

        menuLink.addEventListener('mouseleave', () => {
          const imgId = menuLink.classList[0];
          const image = topicsMegaMenu.querySelector(`[data-imgId="${imgId}"]`);

          if (image) {
            megaMenuImages[0].classList.remove('is-hidden');
            if (menuLink !== megaMenuLinks[0]) image.classList.add('is-hidden');
          }
        });
      });
    }
  }
}

export default MegaMenu;
