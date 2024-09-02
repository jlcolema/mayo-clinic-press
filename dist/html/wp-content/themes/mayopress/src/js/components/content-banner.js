class ContentBanner {
  constructor(element) {
    this.element = element;
    this.postContent = document.querySelector('#post-content');
    this.postContentChildren = document.querySelectorAll('#post-content > *');

    this.moveBanner();
  }

  moveBanner() {
    if (this.postContentChildren.length > 4) {
      const halfPoint = Math.ceil(this.postContentChildren.length / 2);
      this.postContentChildren[halfPoint].after(this.element);
    } else {
      this.postContent.append(this.element);
    }
  }
}

export default ContentBanner;
