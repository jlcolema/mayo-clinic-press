class PostToggle {
  constructor(element) {
    this.element = element;
    this.addEvents();
  }

  addEvents() {
    this.element.addEventListener('click', e => {
      const dataTarget = e.target.closest('[data-value]');
      if (dataTarget && !dataTarget.classList.contains('disabled')) {
        const { value } = dataTarget.dataset;
        const facetwpContainer = document.querySelector('.facetwp-template__container');
        const searchListing = document.querySelector('.search-listing__container');

        if (!facetwpContainer.classList.contains(value)) {
          if (value === 'post') {
            facetwpContainer.classList.remove('product', 'list');
            facetwpContainer.classList.add(value, 'grid');
            searchListing.classList.remove('product');
            searchListing.classList.add(value);
          } else {
            facetwpContainer.classList.remove('post');
            facetwpContainer.classList.add(value);
            searchListing.classList.remove('post');
            searchListing.classList.add(value);
          }
        } else {
          e.preventDefault();
          e.stopPropagation();
        }
      }
    });
  }
}

export default PostToggle;
