import { setAttributes, removeAttributes } from '../utils';

class Drawer {
  constructor(element, index) {
    this.element = element;
    this.index = index;
    this.shouldCloseOthers = this.element.dataset.autoclose === 'true';
    this.widgets = this.element.querySelectorAll('.widget_block');
    this.closeButtons = this.element.querySelectorAll('.mobile-drawer__close-btn');

    if (this.widgets.length) {
      this.widgets.forEach(widget => {
        this.updateWidgetLayout(widget);
      });
    }

    this.config();
    this.mount();
  }

  updateWidgetLayout(widget) {
    if (!widget) return;

    // move shop-promo__box to end of submenu__list
    const shopPromoBox = widget.querySelector('.shop-promo__box');
    if (shopPromoBox) {
      widget.closest('ul').append(shopPromoBox);
    }

    // create new drawer menu from widget elements
    const newWidgetElem = document.createElement('ul');
    const newWidgetAttributes = {
      class: 'menu menu--mobile mobile-drawer js-mobile-drawer',
      'data-animation': 'on',
      'data-multi-items': 'off'
    };
    setAttributes(newWidgetElem, newWidgetAttributes);
    const widgetTitles = widget.querySelectorAll('p');
    const widgetChildren = widget.querySelectorAll('p + ul');
    let newWidgetElemHTML = '';

    widgetTitles.forEach((title, i) => {
      if (title.nextElementSibling && title.nextElementSibling.tagName === 'UL') {
        newWidgetElemHTML += `
          <li class="mobile-drawer__item accordion__item js-mobile-drawer__item">
            <button class="reset mobile-drawer__header accordion__header js-mobile-drawer__trigger js-tab-focus">
              <span class="menu__text widget__title">
                ${title.textContent}
              </span>
    
              <svg class="icon icon--chevron-right" aria-hidden="true" focusable="false">
                <use xlink:href="#chevron-right"></use>
              </svg>
            </button>
    
            <section class="drawer mobile-drawer__panel js-mobile-drawer__panel">
              <div class="drawer__content mobile-drawer__panel-content">
                <ul class="submenu__list">
                  <li class="submenu__item widget__title mobile-drawer__close-btn">
                    <svg class="icon icon--chevron-left" aria-hidden="true" focusable="false">
                      <use xlink:href="#chevron-left"></use>
                    </svg>

                    <button class="submenu__link reset">
                      ${title.textContent}
                    </button>
                  </li>
        `;

        widgetChildren[i].querySelectorAll('a').forEach(childLink => {
          const text = childLink.textContent;
          const link = childLink.href;

          if (text && link) {
            newWidgetElemHTML += `
                  <li class="submenu__item accordion__item mobile-drawer__item">
                    <a class="submenu__link" href="${link}">
                      ${text}
                    </a>
                  </li>
            `;
          }
        });

        newWidgetElemHTML += `
                </ul>
              </div>
            </section>
          </li>
        `;
      }
    });

    newWidgetElem.innerHTML = newWidgetElemHTML;

    widget.replaceWith(newWidgetElem);

    // add new drawer close buttons
    this.closeButtons = this.element.querySelectorAll('.mobile-drawer__close-btn');
  }

  config() {
    this.items = [...this.element.querySelectorAll('.js-mobile-drawer__item')];
    this.showClass = 'drawer--is-visible';
    this.mockTopics = this.element.querySelector('.mock-topics');
    this.topicsDrawerLink = this.element.querySelector('.topics--mobile-parent > button');
    // Bind event and save so it can be referenced
    this.triggerDrawer = this.triggerDrawer.bind(this);
    this.prevDrawer = this.prevDrawer.bind(this);
  }

  mount() {
    // Set initial aria attributes
    this.setAttributes();

    // Listen for Drawer events
    this.bindEvents();

    // find final drawer items
    this.finalDrawer(this.element, 0);

    // set up mock topics link
    this.topicsDrawer();
  }

  unmount() {
    this.removeAttributes();
    this.unbindEvents();
  }

  bindEvents() {
    this.element.addEventListener('click', this.triggerDrawer);

    if (this.closeButtons.length) {
      this.closeButtons.forEach(closeBtn => {
        closeBtn.addEventListener('click', this.prevDrawer);
      });
    }
  }

  unbindEvents() {
    this.element.removeEventListener('click', this.triggerDrawer);

    if (this.closeButtons.length) {
      this.closeButtons.forEach(closeBtn => {
        closeBtn.addEventListener('click', this.prevDrawer);
      });
    }
  }

  setAttributes() {
    this.items.forEach((item, i) => {
      const trigger = item.querySelector('.js-mobile-drawer__trigger');
      const content = item.querySelector('.js-mobile-drawer__panel');
      const isOpen = item.querySelector('.drawer')?.classList.contains(this.showClass) ? 'true' : 'false';

      const triggerAttributes = {
        'aria-expanded': isOpen,
        'aria-controls': `mobile-drawer-content-${this.index}-${i}`,
        id: `mobile-drawer-header-${this.index}-${i}`,
      };

      // Add button attributes if not a button
      if (trigger.tagName !== 'BUTTON') {
        triggerAttributes.tabindex = '0';
        triggerAttributes.role = 'button';
      }

      // Add drawer plus icon if an icon doesn't exist
      if (!trigger.querySelector('.icon')) {
        trigger.insertAdjacentHTML('beforeend', '<svg class="icon mobile-drawer__icon-plus no-js:is-hidden" viewBox="0 0 20 20" aria-hidden="true"> <g class="icon__group" fill="none" stroke="currentColor"> <line x1="2" y1="10" x2="18" y2="10"></line> <line x1="10" y1="18" x2="10" y2="2"></line> </g> </svg>');
      }

      setAttributes(trigger, triggerAttributes);
      setAttributes(content, {
        'aria-labelledby': `mobile-drawer-header-${this.index}-${i}`,
        id: `mobile-drawer-content-${this.index}-${i}`,
      });
    });
  }

  removeAttributes() {
    this.items.forEach(item => {
      const trigger = item.querySelector('.js-mobile-drawer__trigger');
      const content = item.querySelector('.js-mobile-drawer__panel');

      removeAttributes(trigger, ['aria-expanded', 'aria-controls', 'id', 'role', 'tabindex']);
      removeAttributes(content, ['aria-labelledby', 'id']);
    });
  }

  triggerDrawer({ target }) {
    if (target.closest('a')) return;

    const trigger = target.closest('.js-mobile-drawer__trigger');
    // Check index to make sure the click didn't happen inside a child drawer
    if (!trigger || this.items.indexOf(trigger.parentElement) === -1) return;

    const bool = trigger.getAttribute('aria-expanded') === 'true';
    if (this.shouldCloseOthers) {
      const openedDrawerTrigger = document.querySelector('.drawer--is-visible .js-mobile-drawer__trigger');
      if (openedDrawerTrigger && openedDrawerTrigger !== trigger) {
        this.animateDrawer(openedDrawerTrigger, true, false);
      }
    }
    this.animateDrawer(trigger, bool, false);
  }

  prevDrawer({ target }) {
    const trigger = target.closest('.mobile-drawer__item').querySelector('.js-mobile-drawer__trigger');

    // Check index to make sure the click didn't happen inside a child drawer
    if (!trigger || this.items.indexOf(trigger.parentElement) === -1) return;

    const bool = trigger.getAttribute('aria-expanded') === 'true';
    if (this.shouldCloseOthers) {
      const openedDrawerTrigger = document.querySelector('.drawer--is-visible .js-mobile-drawer__trigger');
      if (openedDrawerTrigger && openedDrawerTrigger !== trigger) {
        this.animateDrawer(openedDrawerTrigger, true, false);
      }
    }
    this.animateDrawer(trigger, bool, false);

    // remove --set-height and re-add if needed
    document.querySelector('#mobile-navigation').removeAttribute('style');
    const openDrawers = this.element.querySelectorAll('.drawer--is-visible');
    if (openDrawers.length) {
      const menuHeight = openDrawers[openDrawers.length - 1].querySelector('.submenu__list').offsetHeight;

      if (menuHeight) {
        document.querySelector('#mobile-navigation').setAttribute('style', `--set-height: ${menuHeight}px`);

        document.querySelectorAll('.drawer').forEach(drawer => {
          if (drawer.querySelector('.drawer__content').scrollTop !== 0) {
            drawer.querySelector('.drawer__content').scrollTop = 0;
          }
        });
      }
    }
  }

  animateDrawer(trigger, bool) {
    const item = trigger.closest('.js-mobile-drawer__item');
    const content = item.querySelector('.js-mobile-drawer__panel');

    if (!bool) {
      item.querySelector('.drawer')?.classList.add(this.showClass);
      const menuHeight = item.querySelector('.drawer .submenu__list').offsetHeight;

      if (menuHeight) {
        document.querySelector('#mobile-navigation').setAttribute('style', `--set-height: ${menuHeight}px`);

        document.querySelectorAll('.drawer').forEach(drawer => {
          if (drawer.querySelector('.drawer__content').scrollTop !== 0) {
            drawer.querySelector('.drawer__content').scrollTop = 0;
          }
        });
      }
    }
    trigger.setAttribute('aria-expanded', !bool);
    this.resetContentVisibility(item, content, bool);
  }

  resetContentVisibility(item, content, bool) {
    item.querySelector('.drawer')?.classList.toggle(this.showClass, !bool);
    content.removeAttribute('style');
  }

  finalDrawer(drawer, i) {
    const nestedDrawers = drawer.querySelectorAll('.drawer');

    if (nestedDrawers.length) {
      i++;
      nestedDrawers.forEach(nestedDrawer => {
        this.finalDrawer(nestedDrawer, i);
      });
    } else {
      drawer.classList.add('drawer--final');
    }
  }

  topicsDrawer() {
    if (!this.mockTopics || !this.topicsDrawerLink) return;

    this.mockTopics.addEventListener('click', () => {
      this.topicsDrawerLink.click();
    });
  }
}

export default Drawer;
