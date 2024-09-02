class VariationRadios {
  constructor(element) {
    this.element = element;
    this.clickableElements = this.element.querySelectorAll('label');
    this.clearVariation = document.querySelector('.reset_variations');
    this.priceContainer = document.querySelector('.qty--price');
    this.selectVariations = document.querySelectorAll('.variations select');
    this.formatLabel = this.element.closest('tr').querySelector('label');

    if (this.clearVariation) {
      this.clearClick();
    }

    if (this.clickableElements.length) {
      this.addToLabel();
      this.clickableElements.forEach(label => {
        this.addBreak(label);
        this.addEvents(label);
      });
    }

    this.updateVariations();
  }

  clearClick() {
    setTimeout(() => {
      if (this.clearVariation.style.visibility === 'visible') {
        this.clearVariation.click();
      }
    }, 500);
  }

  addToLabel() {
    this.formatLabel.insertAdjacentHTML('beforeend', '<span class="selected-format__label"></span>');
  }

  addBreak(label) {
    const innerHtmlArr = label.innerHTML.split('---');
    label.innerHTML = `${innerHtmlArr[0]}<div class="price-per-period">${innerHtmlArr[1]}</div>`;
  }

  addEvents(label) {
    label.addEventListener('click', e => {
      const clickedTarget = e.target.closest('label');
      const prevChecked = this.element.querySelector('label.checked');
      if (clickedTarget !== prevChecked) {
        const clickedValue = clickedTarget.querySelector('input').value;
        const selectId = `#${clickedTarget.closest('tr').querySelector('select').id}`;

        if (prevChecked) {
          prevChecked.classList.remove('checked');
        }

        clickedTarget.classList.add('checked');

        this.element.style = '--outline-color: var(--color-contrast-medium)';

        const selectedPrice = clickedTarget.querySelector('.price-per-period').textContent;
        this.priceContainer.textContent = ` - ${selectedPrice}`;

        const qtyContainer = document.querySelector('.quantity__full--container');
        const selectedPriceOnly = selectedPrice.split(' ')[0].replace('$', '');
        qtyContainer.dataset.price = selectedPriceOnly;

        const selectElem = jQuery(selectId);
        selectElem[0].value = clickedValue;
        selectElem.trigger('change');

        const qtyValue = qtyContainer.querySelector('input[type="number"]') ? qtyContainer.querySelector('input[type="number"]').value : 0;
        if (qtyValue > 1) {
          const qtyPrice = document.querySelector('.qty--price');
          const updatedPrice = parseFloat(selectedPriceOnly * qtyValue).toFixed(2);

          if (qtyPrice) {
            qtyPrice.textContent = `$${updatedPrice} / year`;
          }
        }

        this.formatLabel.querySelector('.selected-format__label').innerHTML = `: <span style="font-weight: 400">${clickedValue}</span>`;
      }

      document.querySelector('.single_variation_wrap #wl-wrapper').style.display = 'block';
    });
  }

  updateVariations() {
    if (this.selectVariations.length) {
      this.selectVariations.forEach(selectContainer => {
        const selectObserver = new MutationObserver(() => {
          if (selectContainer.querySelector('.attached')) {
            this.updateVariation();
            selectObserver.disconnect();
          }
        });

        if (selectContainer.querySelector('.attached')) {
          this.updateVariation();
        } else {
          selectObserver.observe(selectContainer, {
            subtree: true,
            childList: true,
            attributes: true
          });
        }
      });
    }
  }

  updateVariation() {
    if (this.clickableElements.length) {
      this.clickableElements.forEach(label => {
        const inputValue = label.querySelector('input')?.value;
        const labelOption = document.querySelector(`option[value="${inputValue ?? ''}"]`);

        if (labelOption && !labelOption.classList.contains('enabled')) {
          label.classList.add('disabled');
        }
      });
    }
    document.querySelector('.variation-radios').classList.add('variations--visible');
  }
}

export default VariationRadios;
