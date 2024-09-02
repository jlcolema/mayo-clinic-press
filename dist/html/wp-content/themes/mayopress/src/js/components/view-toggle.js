class ViewToggle {
  constructor(element) {
    this.element = element;
    this.triggers = this.element.querySelectorAll('button.view__option');

    if (this.triggers) {
      this.addEvents();
    }
  }

  addEvents() {
    this.triggers.forEach(viewButton => {
      viewButton.addEventListener('click', () => {
        const { view } = viewButton.dataset;
        const facetwpContainer = document.querySelector('.facetwp-template__container');

        if (!facetwpContainer.classList.contains(view)) {
          if (view === 'grid') {
            facetwpContainer.classList.remove('list');
            facetwpContainer.classList.add(view);
          } else {
            facetwpContainer.classList.remove('grid');
            facetwpContainer.classList.add(view);
          }

          if (window.history.pushState) {
            this.updateQuery(view);
          }
        }
      });
    });
  }

  updateQuery(selectedView) {
    const url = new URL(window.location.href);
    const { searchParams } = url;
    searchParams.set('view', selectedView);
    url.search = searchParams.toString();
    const newState = url.toString();
    window.history.pushState(null, null, newState);
  }
}

export default ViewToggle;
