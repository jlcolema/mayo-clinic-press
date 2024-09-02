class FormatSortSelect {
  constructor() {
    this.sortContainers = document.querySelectorAll('.sort__container, .onboarding__step-3, form.edit-account');

    this.createFauxSelect();
    document.querySelector('body').addEventListener('click', this.addClick.bind(this));
  }

  createFauxSelect() {
    this.sortContainers.forEach(sortContainer => {
      const sortSelect = sortContainer.querySelector('select');
      const sortSelectValue = sortSelect.value;
      const sortSelectLabel = sortSelect.querySelector(`option[value="${sortSelectValue}"]`)?.textContent.trim();
      const sortOptions = sortSelect.querySelectorAll('option');
      let fauxSelectMarkup = `
        <div class="select--box">
          <p class="options--selected">${sortSelectLabel}</p>
          <div class="options--available">
      `;
      sortOptions.forEach(sortOption => {
        if (sortOption.value) {
          fauxSelectMarkup += `
            <p class="option${sortOption.value === sortSelectValue ? ' selected' : ''}" data-value="${sortOption.value}">${sortOption.textContent.trim()}</p>
          `;
        }
      });
      fauxSelectMarkup += '</div>';
      if (!sortContainer.querySelector('.sort__select--arrows')) {
        fauxSelectMarkup += `
          <div class="sort__select--arrows">
            <svg class="icon icon--chevron-up" aria-hidden="true" focusable="false"><use xlink:href="#chevron-up"></use></svg>
            <svg class="icon icon--chevron-down" aria-hidden="true" focusable="false"><use xlink:href="#chevron-down"></use></svg>
          </div>
        `;
      }
      fauxSelectMarkup += '</div>';
      fauxSelectMarkup = fauxSelectMarkup.trim();

      if (sortSelect.closest('.facetwp-type-sort')) {
        sortSelect.closest('.facetwp-type-sort').insertAdjacentHTML('afterend', fauxSelectMarkup);
      } else {
        sortSelect.insertAdjacentHTML('afterend', fauxSelectMarkup);
      }

      // set width of select if less than available options container
      const selectBox = sortSelect.closest('.facetwp-type-sort') ? sortSelect.closest('.facetwp-type-sort').nextElementSibling : sortSelect.nextElementSibling;
      const optionsAvailable = selectBox.querySelector('.options--available');
      if (selectBox.offsetWidth <= optionsAvailable.offsetWidth) {
        selectBox.setAttribute('style', `--width: ${optionsAvailable.offsetWidth + 30}px`);
        optionsAvailable.setAttribute('style', `--width: ${optionsAvailable.offsetWidth + 30}px`);
      } else if (selectBox.offsetWidth > optionsAvailable.offsetWidth) {
        selectBox.setAttribute('style', `--width: ${selectBox.offsetWidth}px`);
        optionsAvailable.setAttribute('style', `--width: ${selectBox.offsetWidth}px`);
      }
    });
  }

  addClick(e) {
    const { target } = e;
    if (!target.closest('.select--box')) {
      this.closeSelect();
      return;
    }

    const optionTarget = target.closest('p');

    if (target.closest('.select--box').classList.contains('open')) {
      this.closeSelect();
    } else {
      target.closest('.select--box').classList.add('open');
    }

    if (optionTarget && !optionTarget.classList.contains('select--box') && !optionTarget.classList.contains('selected')) {
      const { value } = optionTarget.dataset;
      const label = optionTarget.textContent.trim();

      // change select value for all FacetWP sort selects before refreshing FWP
      this.sortContainers.forEach(sortContainer => {
        if (sortContainer.querySelector('.selected')) sortContainer.querySelector('.selected').classList.remove('selected');
        sortContainer.querySelector(`[data-value="${value}"]`).classList.add('selected');

        sortContainer.querySelector('.options--selected').textContent = label;
        sortContainer.querySelector('select').value = value;
      });

      FWP.refresh();
    }
  }

  closeSelect() {
    if (document.querySelector('.select--box.open')) document.querySelector('.select--box.open').classList.remove('open');
  }
}

export default FormatSortSelect;
