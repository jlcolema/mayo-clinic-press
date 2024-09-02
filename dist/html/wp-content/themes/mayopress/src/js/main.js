import Emitter from './utils/emitter';
import MediaQuerySync from './utils/mqsync';
import toggleComponentByViewport from './utils/component-viewports';
import StickyEmailCapture from './components/footer-email';
import gatekeeper from './utils/sfg-gatekeeper';
import customSplide from './utils/custom-splide';
import privacyPolicy from './utils/privacy-policy';
import ShopPromo from './utils/shop-promo';
import addBtnIcons from './utils/btn-icons';
import SnapSlider from './blocks/post-carousel';
import { buttonFunction, accountFunction } from './utils/cbx-scripts';

window.theme = {};
const { theme } = window;

// Pubsub
theme.emitter = new Emitter();

// CSS breakpoint listener
theme.viewport = new MediaQuerySync({
  sm: '(min-width: 48rem)', // ~768px
  md: '(min-width: 64rem)', // ~1024px
  lg: '(min-width: 80rem)', // ~1280px
});

const { viewport } = theme;

/**
 * Dynamic component loading. Find data-component elements
 * and import/execute component JavaScript, with a little
 * webpack JS chunking and resolving magic.
 */
const loadComponent = async (name, element, index) => {
  // Dynamically import the component from `./components/<component-name>.js`
  const { default: Component } = await import(
    /* webpackChunkName: 'component-[request]' */
    `./components/${name}`
  );

  return { name, element, instance: new Component(element, index) };
};

/**
 * Select every `data-component` and iterate through them. The reduce method
 * returns an array with a promise of the previous result on each iteration,
 * which needs to be awaited to process the next result.
 */
const components = [...document.querySelectorAll('[data-component]')]
  .reduce(async (memo, element, index) => [
    ...await memo,
    ...await Promise.all(element.dataset.component.split(' ')
      .map(name => loadComponent(name, element, index)))
  ], []);

// Store all component instances and run any subsequent functions
components
  .then(values => { theme.componentStore = values; })
  .then(() => {
    // Component is accordion and has data-mobile-accordion attribute
    const accordionFilter = ({ name, element }) => (
      name === 'accordion' && element.hasAttribute('data-mobile-accordion')
    );

    // Toggle accordions for mobile/desktop
    viewport.watch('md', () => {
      toggleComponentByViewport(
        accordionFilter,
        !viewport.is('md'),
        theme.componentStore
      );
    });
  });

// sticky email capture
if (document.querySelector('footer')) new StickyEmailCapture(document.querySelector('footer'));

// check subscriptions
if (window.location.pathname.includes('/my-account/')) {
  gatekeeper();
}

// fix gallery
customSplide();

// privacy policy check
privacyPolicy(document.querySelector('#mc-consent-advisory'));

// make shop promo card clickable
const shopPromos = document.querySelectorAll('.shop-promo__box');
if (shopPromos.length) {
  shopPromos.forEach(shopPromoCard => {
    new ShopPromo(shopPromoCard);
  });
}

// add button icons where applicable
addBtnIcons();

// instantiate SnapSlier
document.querySelectorAll('[data-snap-slider]').forEach(slider => {
  new SnapSlider(slider);
});

// Add `role` and `aria-label` to `.facetwp-flyout`
// to fix the ARIA landmark issue
const flyoutObserver = new MutationObserver(mutationsList => {
  for (const mutation of mutationsList) {
    if (mutation.type === 'childList') {
      const flyout = document.querySelector('.facetwp-flyout');
      if (flyout) {
        flyout.setAttribute('role', 'region');
        flyout.setAttribute('aria-label', 'Filter and Sort');
      }
    }
  }
});

flyoutObserver.observe(document.body, { childList: true, subtree: true });

// Add Order Summary toggle for the Order Confirmation page
const orderSummaryToggle = document.querySelector('.thanks-summary-header');
const orderSummary = document.querySelector('.thanks-summary');
if (orderSummaryToggle && orderSummary) {
  orderSummaryToggle.addEventListener('click', () => {
    orderSummary.classList.toggle('thanks-summary--is-open');
  });
}

// eslint-disable-next-line no-undef
jQuery(document).ready($ => {
  function updateCartCount() {
    $.ajax({
      // eslint-disable-next-line no-undef
      url: myAjax.ajaxurl,
      type: 'POST',
      data: {
        action: 'get_cart_count'
      },
      success(response) {
        $('.header__cart-count').text(response); // Update the cart count element
      }
    });
  }

  // Call the function on page load
  updateCartCount();
});

// Custom CBX & Favorite plugin -- Button
if (document.querySelector('body').classList.contains('single-post')) buttonFunction();
// Custom CBX & Favorite plugin -- My Library
if (window.location.pathname.includes('my-library')) accountFunction();

// redirect to original page after signing in
document.addEventListener('DOMContentLoaded', () => {
  const signInLinks = document.querySelectorAll('.navbar--sign-in');
  if (signInLinks.length) {
    signInLinks.forEach(signInLink => {
      signInLink.href += `?redirect_to=${encodeURIComponent(window.location.href)}`;
    });
  }
});
