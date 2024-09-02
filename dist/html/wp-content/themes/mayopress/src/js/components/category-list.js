class CategoryList {
  constructor(element) {
    this.element = element;
    this.mobileScrollElement = this.element.querySelector('.facetwp-facet-listing_categories');
    this.banner = document.querySelector('.archives__banner');
    this.checkedCategory = this.element.querySelector('.checked');

    if (!cat) {
      this.addAllCount();
      this.hideExtraFacets();
      this.observeElement();
      this.listenElement();
      this.getOriginalBanner();
    } else {
      this.getOriginalBanner();
      this.catPage();
    }
  }

  observeElement() {
    const watchedObserver = new MutationObserver(() => {
      this.checkedCategory = this.element.querySelector('.checked');

      this.toggleFeatured();
      this.changeBanner();
      this.addAllCount(watchedObserver);
      this.hideExtraFacets(watchedObserver);
    });

    watchedObserver.observe(this.element, {
      subtree: true,
      childList: true,
      attributes: true
    });
  }

  toggleFeatured() {
    const featuredElem = document.querySelector('.featured-post');

    if (featuredElem) {
      if (this.checkedCategory && !this.checkedCategory.getAttribute('data-value')) {
        featuredElem.removeAttribute('style');
      } else {
        featuredElem.style.display = 'none';
      }
    }
  }

  changeBanner() {
    const bannerValue = (this.checkedCategory && this.checkedCategory.getAttribute('data-value'))
      ? this.checkedCategory.getAttribute('data-value')
      : this.caption;

    const bannerCaption = (this.checkedCategory && this.checkedCategory.getAttribute('data-value') && this.checkedCategory.querySelector('.facetwp-display-value').textContent)
      ? this.checkedCategory.querySelector('.facetwp-display-value').textContent
      : this.caption;

    const bannerTitle = banner_options[`${bannerValue}_banner_title`]
      ? banner_options[`${bannerValue}_banner_title`]
      : this.title;

    const bannerText = banner_options[`${bannerValue}_banner_text`]
      ? banner_options[`${bannerValue}_banner_text`]
      : this.text;

    if (this.captionElement) this.captionElement.textContent = bannerCaption.replace('-', ' ');
    if (this.titleElement) this.titleElement.textContent = bannerTitle;
    if (this.textElement) this.textElement.innerHTML = bannerText;

    this.updateSearchBar(bannerValue);
  }

  updateSearchBar(bannerValue) {
    if (bannerValue === 'MAYO CLINIC PRESS TOPICS') {
      bannerValue = 'topics';
    } else {
      bannerValue += ' topics';
    }

    const searchBar = document.querySelector('.facetwp-facet-topics_search .facetwp-search');
    if (searchBar) {
      searchBar.setAttribute('placeholder', `Search ${bannerValue.replace('-', ' ')}...`);
    }
  }

  listenElement() {
    if (this.mobileScrollElement) {
      this.element.addEventListener('touchend', () => {
        const prevScrollLeft = this.mobileScrollElement.scrollLeft;
        let styleString = '';

        if (this.mobileScrollElement.scrollLeft < 10) {
          styleString += '--fade-begin: 0%;';
        } else {
          styleString += '--fade-begin: 25%;';
        }

        this.mobileScrollElement.scrollLeft += 1;
        if (prevScrollLeft === this.mobileScrollElement.scrollLeft) {
          styleString += '--fade-end: 100%;';
        } else {
          styleString += '--fade-end: 75%;';
        }

        this.element.style = styleString;
      });
    }
  }

  getOriginalBanner() {
    if (this.banner) {
      this.captionElement = this.banner.querySelector('.archives__icon--caption');
      this.caption = this.captionElement.textContent;

      this.titleElement = this.banner.querySelector('.archives__banner--title');
      this.title = this.titleElement.textContent;

      this.textElement = this.banner.querySelector('.archives__banner--text');
      this.text = this.textElement.innerHTML;
    }
  }

  addAllCount(observer) {
    const allFacet = this.element.querySelector('.facetwp-radio[data-value=""]');
    const counters = this.element.querySelectorAll('.facetwp-counter');

    if (allFacet && counters) {
      if (observer) {
        observer.disconnect();
      }

      let totalCount = 0;

      counters.forEach(counter => {
        const count = parseInt(counter.textContent.replace('(', '').replace(')', ''), 10);
        totalCount += count;
      });

      if (!allFacet.innerHTML.includes('facetwp-counter')) {
        allFacet.innerHTML = `<span class="facetwp-display-value">${allFacet.textContent}</span><span class="facetwp-counter">(${totalCount})</span>`;
      }

      if (observer) {
        observer.observe(this.element, {
          subtree: true,
          childList: true,
          attributes: true
        });
      }
    }
  }

  hideExtraFacets(observer) {
    const categoryListing = this.element.querySelector('.facetwp-facet-listing_categories');
    const allCategories = this.element.querySelectorAll('.facetwp-facet-listing_categories > .facetwp-radio');
    const extraContainer = this.element.querySelector('.category-list__extra-container');

    if (categoryListing && allCategories.length > 6 && !extraContainer) {
      if (observer) {
        observer.disconnect();
      }

      const hiddenCategories = document.createElement('div');
      hiddenCategories.classList.add('category-list__extra-container');

      for (let i = allCategories.length - 1; i > 5; i--) {
        hiddenCategories.prepend(allCategories[i]);
      }

      categoryListing.insertAdjacentHTML('beforeend', '<span class="extra-category__toggle"></span>');
      categoryListing.append(hiddenCategories);

      this.extraFacetsToggle();

      if (observer) {
        observer.observe(this.element, {
          subtree: true,
          childList: true,
          attributes: true
        });
      }
    }
  }

  extraFacetsToggle() {
    const toggleLink = this.element.querySelector('.extra-category__toggle');

    if (toggleLink) {
      toggleLink.addEventListener('click', e => {
        const toggleTarget = e.target.closest('.extra-category__toggle');
        if (toggleTarget.classList.contains('extra-category__toggle--is-open')) {
          toggleTarget.classList.remove('extra-category__toggle--is-open');
        } else {
          toggleTarget.classList.add('extra-category__toggle--is-open');
        }
      });
    }
  }

  catPage() {
    const bannerValue = cat || this.caption;

    const bannerCaption = cat || this.caption;

    const bannerTitle = banner_options[`${catSlug}_banner_title`]
      ? banner_options[`${catSlug}_banner_title`]
      : this.title;

    const bannerText = banner_options[`${catSlug}_banner_text`]
      ? banner_options[`${catSlug}_banner_text`]
      : this.text;

    if (this.captionElement) this.captionElement.textContent = bannerCaption.replace('-', ' ');
    if (this.titleElement) this.titleElement.textContent = bannerTitle;
    if (this.textElement) this.textElement.innerHTML = bannerText;

    this.updateSearchBar(bannerValue);
  }
}

export default CategoryList;
