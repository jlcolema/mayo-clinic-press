import { setCookie, getCookie } from '../utils';

class SetTour {
  constructor(element) {
    this.element = element;
    this.body = document.querySelector('body');
    this.mainContent = document.querySelectorAll('#main .singular__content > div');
    this.showTour = window.location.search.includes('show_tour');
    this.endTour = getCookie('endTour');
    this.tourModal = null;
    this.tourTooltip = null;
    this.i = 0;
    this.helpfulHint = 'This is the “Featured” carousel where you will find our most popular stories, based on your specific interests. We will recommend you content on trending health topics, medical breakthroughs, book releases, and more.';

    if (this.showTour && !this.endTour && this.mainContent.length) {
      this.hideStickyFooter();
      this.startTour();
    }
  }

  startTour() {
    if (this.body) this.body.classList.add('no--scroll');
    this.element.classList.remove('is-hidden');
    this.element.insertAdjacentHTML('afterbegin', `
      <div class="modal modal--animate-scale modal--is-visible modal--tour">
        <div class="modal__content" role="alertdialog">
          <h2>Let’s explore your new personalized homepage.</h2>
          <p>Learn about all the special features that make up your personalized experience through your new Mayo Clinic Press account.</p>
          <a href="javascript:void(0);" class="btn btn--primary tour__go-to--2">Start Tour</a>
          <a href="javascript:void(0);" class="reset tour__close">Skip Tour</a>
          <a href="javascript:void(0);" class="reset tour__close modal__close-btn modal__close-btn--text js-modal__close">Skip Tour</a>
        </div>
      </div>`);

    this.tourModal = this.element.querySelector('.modal--tour');
    this.stepOneEvents();
  }

  stepOneEvents() {
    if (!this.tourModal) this.closeTour();

    this.tourModal.addEventListener('click', e => {
      const { target } = e;

      if (!target.closest('.modal__content') || target.closest('.tour__close')) {
        this.closeTour();
      } else if (target.closest('.tour__go-to--2')) {
        this.tourModal.remove();
        this.nextStep();
      }
    });
  }

  nextStep() {
    if (!this.mainContent[this.i]) this.closeTour();

    const currentStep = document.querySelector('.tour--active-step');
    if (currentStep) currentStep.classList.remove('tour--active-step');

    this.mainContent[this.i].classList.add('tour--active-step');

    if (this.tourTooltip) {
      this.mainContent[this.i].append(this.tourTooltip);
      this.tourTooltip.querySelector('.tooltip__helpful-hint').textContent = this.helpfulHint;
      this.tourTooltip.querySelector('.tooltip__counter').textContent = `${this.i + 1} / 4`;
    } else {
      this.mainContent[this.i].insertAdjacentHTML('beforeend', `
        <div class="tour--tooltip">
          <p class="heading--xs position--top-left">Helpful Hint</p>
          <p class="tooltip__helpful-hint">${this.helpfulHint}</p>
          <div class="tooltip__section--bottom">
            <div class="tooltip__counter">${this.i + 1} / 4</div>
            <a href="javascript:void(0);" class="btn btn--primary">Next</a>
          </div>
          <a href="javascript:void(0);" class="reset tour__close">Skip Tour</a>
        </div>`);

      this.tourTooltip = document.querySelector('.tour--tooltip');
      this.tooltipEvents();
    }

    setTimeout(() => {
      if (window.innerWidth < 768) {
        this.mainContent[this.i].scrollIntoView({ behavior: 'smooth', block: 'start' });
      } else {
        this.mainContent[this.i].scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }, 100);
  }

  tooltipEvents() {
    if (!this.tourTooltip) this.closeTour();

    this.tourTooltip.addEventListener('click', e => {
      const { target } = e;

      if (target.closest('.tour__close')) {
        this.closeTour();
      } else if (target.closest('.btn')) {
        this.i += 1;
        // eslint-disable-next-line default-case
        switch (this.i) {
          case 1:
            this.helpfulHint = 'This is the “Based on Your Interests” section of our site, where you can find the latest content relevant to the topics you are most interested in.';
            break;
          case 2:
            this.helpfulHint = 'In the “Explore Areas of Interest” section, you can navigate to all Mayo Clinic Press content in a specific category. We will make sure to prioritize your preferred topics of interest.';
            break;
          case 3:
            this.helpfulHint = 'Finally, our “Shop” carousel highlights a curated list of our books based on your interests. You can purchase these books directly from our website.';
            this.tourTooltip.querySelector('.tour__close').textContent = 'Close';
            this.tourTooltip.querySelector('.btn').textContent = 'Finished';
            break;
          default:
            this.closeTour();
            return;
        }
        this.nextStep();
      }
    });
  }

  closeTour() {
    if (this.element) this.element.remove();
    if (this.tourTooltip) this.tourTooltip.remove();
    if (this.body) this.body.classList.remove('no--scroll');
    setCookie('endTour', 'true', 36500);
  }

  hideStickyFooter() {
    const stickyFooter = document.querySelector('#email-capture');
    if (stickyFooter) {
      stickyFooter.classList.remove('session--fixed');
      setCookie('stickyEmail', 'true', 0.25);
    }
  }
}

export default SetTour;
