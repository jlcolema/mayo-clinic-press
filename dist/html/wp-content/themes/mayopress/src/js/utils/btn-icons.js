const addBtnIcons = () => {
  const iconButtons = document.querySelectorAll('.btn--star, .btn--heart');
  if (iconButtons.length) {
    iconButtons.forEach(icon => {
      if (icon.classList.contains('btn--star')) {
        icon.querySelector('a').insertAdjacentHTML('afterbegin', '<svg class="icon icon--star-filled" aria-hidden="true" focusable="false"><use xlink:href="#star-filled"></use></svg>');
      } else if (icon.classList.contains('btn--heart')) {
        icon.querySelector('a').insertAdjacentHTML('afterbegin', '<svg class="icon icon--heart-filled" aria-hidden="true" focusable="false"><use xlink:href="#heart-filled"></use></svg>');
      }
    });
  }
};

export default addBtnIcons;
