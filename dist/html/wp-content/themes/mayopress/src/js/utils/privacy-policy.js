import { setCookie } from '.';

const privacyPolicyCheck = () => {
  const privayPolicyConsent = localStorage.getItem('privacyPolicyTimeStamp');
  const currentTimestamp = Date.now();
  const differenceInDays = (currentTimestamp - (window.timestamp * 1000)) / (1000 * 60 * 60 * 24);

  if (differenceInDays <= 60) {
    if (!privayPolicyConsent || privayPolicyConsent < (window.timestamp * 1000)) {
      localStorage.removeItem('privacyPolicyTimeStamp');
      return false;
    }
  }
  return true;
};

const addLocalStorage = e => {
  const currentTimestamp = Date.now();
  localStorage.setItem('privacyPolicyTimeStamp', currentTimestamp);
  e.target.closest('#mc-consent-advisory').classList.add('is-hidden');
};

const privacyPolicy = consentPopup => {
  if (!consentPopup || !window.timestamp) return;

  if (!privacyPolicyCheck()) {
    // remove stickyEmail
    setCookie('stickyEmail', 'true', 2);
    const emailCapture = document.querySelector('#email-capture');
    if (emailCapture) emailCapture.classList.add('policy-consent__active');

    consentPopup.classList.remove('is-hidden');
    const consentBtn = consentPopup.querySelector('button');
    if (consentBtn) consentBtn.addEventListener('click', addLocalStorage);
  }
};

export default privacyPolicy;
