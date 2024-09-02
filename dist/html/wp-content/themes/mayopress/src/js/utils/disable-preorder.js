export default function disablePreorder() {
  if (document.querySelector('body').classList.contains('post-type-product')) {
    const preorderCheckbox = document.querySelector('#_wc_pre_orders_enabled');
    if (preorderCheckbox) {
      preorderCheckbox.setAttribute('disabled', 'disabled');
    }
  }
}
