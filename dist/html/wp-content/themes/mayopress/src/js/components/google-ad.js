class GoogleAd {
  constructor(element) {
    this.adContainer = element;
    this.adId = this.adContainer.dataset.id;
    this.type = this.adContainer.dataset.type;
    this.size = [this.adContainer.dataset.width, this.adContainer.dataset.height];

    this.createScripts();
    this.screenCheck();
  }

  getTargetId(innerScript) {
    const removeBeg = innerScript.split('googletag.defineSlot(')[1];
    const removeEnd = removeBeg.split(').addService')[0];
    const leftoverArr = removeEnd.split(',');
    this.adId = leftoverArr[leftoverArr.length - 1].replaceAll("'", '').replaceAll('"', '').replaceAll(' ', '');
  }

  createMarkup() {
    this.stickyClose = '';

    if (this.type === 'mobile-sticky') {
      this.stickyClose = '<div tabindex="0" role="button" aria-label="Close advertisement" class="close-button">Close<svg class="icon icon--close icon--xs" aria-hidden="true" focusable="false"><use xlink:href="#close"></use></svg></div>';
    }

    this.adCalloutMarkup = '<p class="advertisement-callout">Advertisement</p>';

    this.adMarkup = `<div id='${this.adId}' class="ads" style='min-width: ${this.size[0]}; min-height: ${this.size[1]};'></div>`;

    this.adScript = document.createElement('script');
    this.adScript.type = 'text/javascript';
    this.adScript.innerHTML = `googletag.cmd.push(function() { googletag.display('${this.adId}'); });`;
  }

  createScripts() {
    this.adHeadScript = document.createElement('script');
    this.adHeadScript.type = 'text/javascript';
    // eslint-disable-next-line default-case
    switch (this.type) {
      case 'sidebar':
        if (window.customSidebarAd) {
          this.adHeadScript.innerHTML = window.sidebarHeaderScript;
          this.getTargetId(window.sidebarHeaderScript);
        } else {
          this.adHeadScript.innerHTML = `
            window.googletag = window.googletag || {cmd: []};
            googletag.cmd.push(function() {
              googletag.defineSlot('/123456789/mcpress.mayoclinic.org-top', [[300, 600], [160, 600], [300, 250]], 'banner-ad-sidebar').addService(googletag.pubads());
              googletag.pubads().enableSingleRequest();
              googletag.enableServices();
            });
          `;
        }
        break;
      case 'mobile':
        if (window.customMobileAd) {
          this.adHeadScript.innerHTML = window.mobileHeaderScript;
          this.getTargetId(window.mobileHeaderScript);
        } else {
          this.adHeadScript.innerHTML = `
            window.googletag = window.googletag || {cmd: []};
            googletag.cmd.push(function() {
              googletag.defineSlot('/123456789/MCPress_Mobile_300x250', [300, 250], 'banner-ad-mobile').addService(googletag.pubads());
              googletag.pubads().enableSingleRequest();
              googletag.enableServices();
            });
          `;
        }
        break;
      case 'mobile-sticky':
        if (window.customMobileStickyAd) {
          this.adHeadScript.innerHTML = window.mobileStickyHeaderScript;
          this.getTargetId(window.mobileStickyHeaderScript);
        } else {
          this.adHeadScript.innerHTML = `
            window.googletag = window.googletag || {cmd: []};
            googletag.cmd.push(function() {
              googletag.defineSlot('/123456789/mcpress.mayoclinic.org-top-728x90', [728, 90], 'banner-ad-mobile-sticky').addService(googletag.pubads());
              googletag.pubads().enableSingleRequest();
              googletag.enableServices();
            });
          `;
        }
        break;
      case 'desktop':
        if (window.customDesktopAd) {
          this.adHeadScript.innerHTML = window.desktopHeaderScript;
          this.getTargetId(window.desktopHeaderScript);
        } else {
          this.adHeadScript.innerHTML = `
            window.googletag = window.googletag || {cmd: []};
            googletag.cmd.push(function() {
              googletag.defineSlot('/123456789/mcpress.mayoclinic.org-top-728x90', [728, 90], 'banner-ad-desktop').addService(googletag.pubads());
              googletag.pubads().enableSingleRequest();
              googletag.enableServices();
            });
          `;
        }
        break;
      case 'desktop-wide':
        if (window.customDesktopWideAd) {
          this.adHeadScript.innerHTML = window.desktopWideHeaderScript;
          this.getTargetId(window.desktopWideHeaderScript);
        } else {
          this.adHeadScript.innerHTML = `
            window.googletag = window.googletag || {cmd: []};
            googletag.cmd.push(function() {
              googletag.defineSlot('/123456789/mcpress.mayoclinic.org-top-728x90', [728, 90], 'banner-ad-desktop-wide').addService(googletag.pubads());
              googletag.pubads().enableSingleRequest();
              googletag.enableServices();
            });
          `;
        }
        break;
    }

    this.createMarkup();
  }

  addCloseClick() {
    const stickyClose = this.adContainer.querySelector('.close-button');
    if (!stickyClose) return;

    stickyClose.addEventListener('click', () => {
      this.adContainer.style.bottom = '-100%';
    });
  }

  screenCheck() {
    if ((this.adContainer.dataset.desktop && window.innerWidth >= 768)
      || (this.adContainer.dataset.mobile && window.innerWidth < 768)) {
      this.adContainer.insertAdjacentHTML('afterbegin', `${this.stickyClose}${this.adCalloutMarkup}${this.adMarkup}`);
      document.head.appendChild(this.adHeadScript);
      this.adContainer.querySelector(`#${this.adId}`).appendChild(this.adScript);

      if (this.type === 'mobile-sticky') {
        this.adContainer.classList.add('ad-sticky');
        this.addCloseClick();
      }
    }
  }
}

export default GoogleAd;
