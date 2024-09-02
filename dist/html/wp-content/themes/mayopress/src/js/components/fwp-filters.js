class FWPFilters {
  constructor(element) {
    this.element = element;

    this.FWPLoaded();
    document.addEventListener('facetwp-loaded', this.FWPLoaded.bind(this));
  }

  FWPLoaded() {
    let length = 0;
    Object.keys(FWP.facets).forEach(key => {
      const checkboxes = document.querySelectorAll(`.facetwp-facet-${key} .checked`);
      length += checkboxes.length;
    });

    if (length > 0) {
      this.element.querySelector('.facets--selected-count').innerHTML = `<strong>FILTER BY:</strong> (${length})`;
    }
  }
}

export default FWPFilters;
