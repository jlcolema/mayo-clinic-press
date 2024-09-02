// Cross-Sell Carousel
class CrossSells {
  constructor(element) {
    this.element = element;
    this.carousel = this.element.querySelector('.carousel');
    this.carouselContent = this.element.querySelector('.carousel-content');
    this.slides = this.element.querySelectorAll('.slide');
    this.arrayOfSlides = Array.prototype.slice.call(this.slides);
    this.carouselDisplaying = '';
    this.screenSize = '';
    this.lengthOfSlide = '';
    this.lastSlide = this.carouselContent.lastElementChild.cloneNode(true);
    this.firstSlide = '';
    this.slidesArray = Array.prototype.slice.call(this.slides);
    this.setScreenSize();
    window.addEventListener('resize', this.setScreenSize);
    this.rightNav = this.element.querySelector('.nav-right');
    this.rightNav.addEventListener('click', this.moveLeft);
    this.moving = true;
    this.leftNav = this.element.querySelector('.nav-left');
    this.leftNav.addEventListener('click', this.moveRight);
    this.carouselContent.addEventListener('mousedown', this.seeMovement);
    this.initialX = '';
    this.initialPos = '';
    this.moveSlidesRight();
  }

  addClone() {
    this.lastSlide.style.left = `${-this.lengthOfSlide}px`;
    this.carouselContent.insertBefore(this.lastSlide, this.carouselContent.firstChild);
  }

  removeClone() {
    this.firstSlide = this.carouselContent.firstElementChild;
    this.firstSlide.parentNode.removeChild(this.firstSlide);
  }

  moveSlidesRight() {
    let width = 0;

    this.slidesArray.forEach((el, i) => {
      el.style.left = `${width}px`;
      width += this.lengthOfSlide;
    });
    this.addClone();
  }

  moveSlidesLeft() {
    this.slidesArray = this.slidesArray.reverse();
    let maxWidth = (this.slidesArray.length - 1) * this.lengthOfSlide;

    this.slidesArray.forEach((el, i) => {
      maxWidth -= this.lengthOfSlide;
      el.style.left = `${maxWidth}px`;
    });
  }

  setScreenSize() {
    if (window.innerWidth >= 1024) {
      this.carouselDisplaying = 4;
    } else if (window.innerWidth >= 668) {
      this.carouselDisplaying = 3;
    } else {
      this.carouselDisplaying = 1;
    }
    this.getScreenSize();
  }

  getScreenSize() {
    this.lengthOfSlide = (this.carousel.offsetWidth / this.carouselDisplaying);
    let initialWidth = -this.lengthOfSlide;
    this.slidesArray.forEach(el => {
      el.style.width = `${this.lengthOfSlide}px`;
      el.style.left = `${initialWidth}px`;
      initialWidth += this.lengthOfSlide;
    });
  }

  moveRight() {
    if (this.moving) {
      this.moving = false;
      this.lastSlide = this.carouselContent.lastElementChild;
      this.lastSlide.parentNode.removeChild(this.lastSlide);
      this.carouselContent.insertBefore(this.lastSlide, this.carouselContent.firstChild);
      this.removeClone();
      this.firstSlide = this.carouselContent.firstElementChild;
      this.firstSlide.addEventListener('transitionend', this.activateAgain);
      this.moveSlidesRight();
    }
  }

  activateAgain() {
    this.firstSlide = this.carouselContent.firstElementChild;
    this.moving = true;
    this.firstSlide.removeEventListener('transitionend', this.activateAgain);
  }

  moveLeft() {
    if (this.moving) {
      this.moving = false;
      this.removeClone();
      this.firstSlide = this.carouselContent.firstElementChild;
      this.firstSlide.addEventListener('transitionend', this.replaceToEnd);
      this.moveSlidesLeft();
    }
  }

  replaceToEnd() {
    this.firstSlide = this.carouselContent.firstElementChild;
    this.firstSlide.parentNode.removeChild(this.firstSlide);
    this.carouselContent.appendChild(this.firstSlide);
    this.firstSlide.style.left = `${(this.arrayOfSlides.length - 1) * this.lengthOfSlide}px`;
    this.addClone();
    this.moving = true;
    this.firstSlide.removeEventListener('transitionend', this.replaceToEnd);
  }

  seeMovement(e) {
    this.initialX = e.clientX;
    this.getInitialPos();
    this.carouselContent.addEventListener('mousemove', this.slightMove);
    document.addEventListener('mouseup', this.moveBasedOnMouse);
  }

  slightMove(e) {
    if (this.moving) {
      const movingX = e.clientX;
      const difference = this.initialX - movingX;
      if (Math.abs(difference) < (this.lengthOfSlide / 4)) {
        this.slightMoveSlides(difference);
      }
    }
  }

  getInitialPos() {
    this.initialPos = [];
    this.slidesArray.forEach(el => {
      const left = Math.floor(parseInt(el.style.left.slice(0, -2), 10));
      this.initialPos.push(left);
    });
  }

  slightMoveSlides(newX) {
    this.slidesArray.forEach((el, i) => {
      const oldLeft = this.initialPos[i];
      el.style.left = `${oldLeft + newX}px`;
    });
  }

  moveBasedOnMouse(e) {
    const finalX = e.clientX;
    if (this.initialX - finalX > 0) {
      this.moveRight();
    } else if (this.initialX - finalX < 0) {
      this.moveLeft();
    }
    document.removeEventListener('mouseup', this.moveBasedOnMouse);
    this.carouselContent.removeEventListener('mousemove', this.slightMove);
  }
}

export default CrossSells;
