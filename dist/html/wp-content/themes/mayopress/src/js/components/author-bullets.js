class AuthorBullets {
  constructor(element) {
    this.element = element;
    this.expertiseList = this.element.querySelector('.author-post__expertise--list');
    this.contributionsList = this.element.querySelector('.author-post__contributions--list');
    this.reformatMarkup();
  }

  reformatMarkup() {
    if (this.expertiseList) {
      const currentMarkup = this.expertiseList.querySelector('p');
      if (currentMarkup) {
        let expertiseMarkup = `<div><span>•</span><p> `;
        expertiseMarkup += currentMarkup.innerHTML.split('<br>').join(`</p></div><div><span>•</span><p>`);
        expertiseMarkup += '</p></div>';
        this.expertiseList.innerHTML = expertiseMarkup;
      }
    }
    if (this.contributionsList) {
      const contributionLinks = this.contributionsList.querySelectorAll('a');
      if (contributionLinks.length) {
        contributionLinks.forEach(elem => {
          elem.innerHTML = `<span>•</span> ${elem.innerHTML}`;
        });
      }
    }
  }
}

export default AuthorBullets;
