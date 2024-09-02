class ExpandableSearch {
  constructor(element) {
    this.element = element;
    this.config();
    this.mount();
  }

  config() {
    this.hasContentClass = 'expandable-search__input--has-content';
    // Bind event and save so it can be referenced
    this.keepExpanded = this.keepExpanded.bind(this);
  }

  mount() {
    // Listen for input event
    this.bindEvents();
  }

  unmount() {
    this.unbindEvents();
  }

  bindEvents() {
    this.element.addEventListener('input', this.keepExpanded);
  }

  unbindEvents() {
    this.element.removeEventListener('input', this.keepExpanded);
  }

  keepExpanded({ target }) {
    // Keep input expanded if the input has a value inside
    target.classList.toggle(this.hasContentClass, target.value.length > 0);
  }
}

export default ExpandableSearch;
