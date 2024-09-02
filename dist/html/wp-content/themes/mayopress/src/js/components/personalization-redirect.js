class PersonalizationRedirect {
  constructor(element) {
    this.element = element;
    this.links = this.element.querySelectorAll('a[href="/my-account/"]');
    this.hasClickedBefore = localStorage.getItem('hasClickedLoginRegister');

    this.setRedirect();
  }

  setRedirect() {
    if (this.hasClickedBefore) {
      this.links.forEach(myAccountLink => {
        myAccountLink.href += `?redirect_to=${encodeURIComponent(window.location.href)}`;
      });
    } else {
      this.links.forEach(myAccountLink => {
        myAccountLink.href += '?redirect_to=/my-account/my-topics/';
        myAccountLink.addEventListener('click', () => {
          localStorage.setItem('hasClickedLoginRegister', 'true');
        });
      });
    }
  }
}

export default PersonalizationRedirect;
