class AdPlacement {
  constructor(element) {
    this.adHub = element;
    this.addSticky = this.adHub.getAttribute('data-sticky');
    this.adHubTemplate = document.querySelector('#ad-hub-template') || document.querySelector('#main');
    this.mobileTop = document.querySelector('.header-advertisement-mobile-top');

    if (!this.mobileTop) {
      document.querySelector('.singular__content p').insertAdjacentHTML('afterend', '<div class="header-advertisement-mobile-top"></div>');
      this.mobileTop = document.querySelector('.header-advertisement-mobile-top');
    }

    this.markupVariables();
    this.screenCheck();
    this.adResize(20);
  }

  markupVariables() {
    this.adCalloutMarkup = '<p class="advertisement-callout">Advertisement</p>';
    this.topAdMarkup = '<div id="gpt-ad-mcpress.mayoclinic.org-top"></div>';
    this.botAdMarkup = '<div id="gpt-ad-mcpress.mayoclinic.org-bottom"></div>';
    this.stickyAdMarkup = `
      <div id="mobile-sticky-ad">
        <div class='sticky-ads' id='sticky-ads'>
          <div class='sticky-ads-close'>
            <button id="sticky-ad-close">close X</button>
          </div>
          <div class='sticky-ads-content'>
            <div id="gpt-ad-mcpress.mayoclinic.org-sticky"></div>
            ${this.adCalloutMarkup}
          </div>

        </div>
      </div>
    `.trim();
    this.policyMarkup = `
      <!--Policy text  -->
      <div class="contentbox policy">
        <p>Mayo Clinic does not endorse companies or products. Advertising revenue supports our not-for-profit mission.</p>
        <strong>Advertising &amp; Sponsorship</strong>
        <ul>
          <li><a href="https://www.mayoclinic.org/about-this-site/advertising-sponsorship-policy">Policy</a></li>
          <li><a href="https://www.mayoclinic.org/about-this-site/advertising-sponsorship">Opportunities</a></li>
          <li class="add-choice"><a href="http://www.aboutads.info/choices/">Ad Choices</a></li>
        </ul>
      </div>
    `.trim();
    this.bookMarkup = `
      <!-- Book store would be a content tile. -->
      <div class="contentbox book">
        <h3>Mayo Clinic Press</h3>
        <p>Check out these best-sellers and special offers on books and newsletters from <a href="https://mcpress.mayoclinic.org/?utm_source=MC-DotOrg-Text&utm_medium=Link&utm_campaign=MC-Press&utm_content=MCPRESS">Mayo Clinic Press</a>.</p>
        <ul>
          <li><a href="https://order.store.mayoclinic.com/flex/mmv/ESDIAB1/?utm_source=MC-DotOrg-Text&utm_medium=Link&utm_campaign=Diabetes-Book&utm_content=EDIAB">
            <span class="myc-visuallyhidden">NEW – The Essential Diabetes Book - Mayo Clinic Press</span>
            <span aria-hidden="true">NEW – The Essential Diabetes Book</span>
          </a></li>
          <li><a href="https://order.store.mayoclinic.com/flex/mmv/incon01/?utm_source=MC-DotOrg-Text&utm_medium=Link&utm_campaign=Incontinence-Book&utm_content=INC">
            <span class="myc-visuallyhidden">Mayo Clinic on Incontinence - Mayo Clinic Press</span>
            <span aria-hidden="true">Mayo Clinic on Incontinence</span>
          </a></li>
          <li><a href="https://order.store.mayoclinic.com/flex/mmv/HRBAL02/?utm_source=MC-DotOrg-Text&utm_medium=Link&utm_campaign=Hearing-Book&utm_content=HEAR">
            <span class="myc-visuallyhidden">NEW – Mayo Clinic on Hearing and Balance - Mayo Clinic Press</span>
            <span aria-hidden="true">NEW – Mayo Clinic on Hearing and Balance</span>
          </a></li>
          <li><a href="https://diet.mayoclinic.org/us/diet-assessment/diet-assessment/?profile=true&promo=65-qtr&utm_source=Mayo&utm_medium=Display&utm_campaign=text_link">
            <span class="myc-visuallyhidden">FREE Mayo Clinic Diet Assessment - Mayo Clinic Press</span>
            <span aria-hidden="true">FREE Mayo Clinic Diet Assessment</span>
          </a></li>
          <li><a href="https://order.store.mayoclinic.com/hl/HLFREEB?utm_source=MC-DotOrg-Text&utm_medium=Link&utm_campaign=HealthLetter-Digital&utm_content=HL_FREEBOOK">
            <span class="myc-visuallyhidden">Mayo Clinic Health Letter - FREE book - Mayo Clinic Press</span>
            <span aria-hidden="true">Mayo Clinic Health Letter - FREE book</span>
          </a></li>
        </ul>
      </div>
    `.trim();
  }

  closeStickyAds() {
    const stickyAd = document.getElementById('mobile-sticky-ad');
    stickyAd.style.display = 'none';
  }

  adPlacement(adHubMarkup, mobileMarkup, stickyMarkup) {
    if (adHubMarkup) {
      this.adHub.insertAdjacentHTML('afterbegin', adHubMarkup);
    }
    if (mobileMarkup) {
      this.mobileTop.insertAdjacentHTML('afterbegin', mobileMarkup);
    }
    if (stickyMarkup && this.addSticky) {
      this.adHubTemplate.insertAdjacentHTML('afterbegin', stickyMarkup);
      document.querySelector('#sticky-ad-close').addEventListener('click', this.closeStickyAds);
    }
  }

  screenCheck() {
    if (window.innerWidth < 768) {
      const adHubMarkup = this.adCalloutMarkup + this.botAdMarkup + this.bookMarkup;
      const mobileMarkup = this.adCalloutMarkup + this.topAdMarkup + this.policyMarkup;
      const templateMarkup = this.stickyAdMarkup;

      this.adPlacement(adHubMarkup, mobileMarkup, templateMarkup);
    } else {
      // eslint-disable-next-line max-len
      const adHubMarkup = this.adCalloutMarkup + this.topAdMarkup + this.policyMarkup + this.bookMarkup + this.botAdMarkup;

      this.adPlacement(adHubMarkup);
    }
  }

  adResize(retries) {
    if (retries >= 0) {
      const inputs = document.querySelectorAll('[id^\x3d"google_ads_"][name^\x3d"google_ads_"]');
      if (inputs.length > 0) {
        inputs.forEach((_, index) => {
          try {
            const vHeight = inputs[index].contentWindow.document.documentElement.scrollHeight + 1;
            if (vHeight !== inputs[index].height) {
              inputs[index].setAttribute('height', vHeight);
            }
          } catch (ex) {
            console.log(ex.message);
          }
        });
      }

      setTimeout(() => {
        this.adResize(retries);
      }, 500);
    }
  }
}

export default AdPlacement;
