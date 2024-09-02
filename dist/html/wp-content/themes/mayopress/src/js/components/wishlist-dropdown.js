class Wishlist {
  constructor(element) {
    this.element = element;
    this.wishlistLink = this.element.querySelector('.wl-add-link');
    this.wishlistWrapper = this.element.querySelector('#wl-wrapper');
    this.alreadyWish = this.element.querySelector('.wl-already-in');
    this.wishlist = document.querySelector('.wl-list-pop');

    this.moveWishlist();
    this.modifyWishlistLink();
  }

  moveWishlist() {
    if (this.alreadyWish && this.wishlist) {
      this.wishlist.prepend(this.alreadyWish);
      this.alreadyWish.insertAdjacentHTML('afterend', '<div class="wl-add-to-list__header has-heading-sm-font-size">Add to a Wishlist</div>');
    }

    if (this.wishlistWrapper && this.wishlist) {
      this.wishlistWrapper.addEventListener('click', () => {
        this.wishlistWrapper.append(this.wishlist);
      });
    }
  }

  modifyWishlistLink() {
    if (this.wishlistLink && this.alreadyWish) {
      const wishlistIcon = this.wishlistLink.querySelector('.icon');

      if (wishlistIcon) {
        wishlistIcon.outerHTML = wishlistIcon.outerHTML.replaceAll('heart', 'heart-filled');
      }

      this.wishlistLink.style.color = 'var(--color-primary)';
      this.wishlistLink.insertAdjacentHTML('beforeend', '<svg style="--size: 10px;" class="icon icon--chevron-down" aria-hidden="true" focusable="false"><use xlink:href="#chevron-down"></use></svg>');
    }
  }
}

export default Wishlist;
