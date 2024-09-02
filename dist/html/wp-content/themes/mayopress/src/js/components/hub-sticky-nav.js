class HubStickyNav {
  constructor(element) {
    this.element = element;
    this.mainIds = document.querySelectorAll('#main .singular__content > [id]');
    this.icons = { podcasts: 'mic', stories: 'chat' };

    this.initNav();
  }

  initNav() {
    if (this.mainIds.length === 0) return;

    let navMarkup = '<ul role="list">';

    this.mainIds.forEach(section => {
      const sectionId = section.getAttribute('id');
      const navLabel = sectionId.replace('rise-', '');

      navMarkup += `
        <li class="hub-nav__menu-item ${sectionId}">
          <a href="#${sectionId}">
            <svg class="icon icon--${this.icons[navLabel]} icon--xxs" aria-hidden="true" focusable="false">
              <use xlink:href="#${this.icons[navLabel]}"></use>
            </svg>
            ${navLabel}
          </a>
        </li>
      `;
    });

    navMarkup += '<li class="hub-nav__menu-item hub-nav__menu-label">Explore:</li></ul>';

    this.element.innerHTML = navMarkup;
  }
}

export default HubStickyNav;
