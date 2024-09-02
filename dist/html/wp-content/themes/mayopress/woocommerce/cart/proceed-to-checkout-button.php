<?php
/**
 * Proceed to checkout button
 *
 * Contains the markup for the proceed to checkout button on the cart.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/proceed-to-checkout-button.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button btn btn--primary">
  <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m23 14v-5a7 7 0 0 0 -14 0v5h-3v16h20v-16zm-12-5a5 5 0 0 1 10 0v5h-10zm13 19h-16v-12h16z"/></svg>
	<?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
</a>
