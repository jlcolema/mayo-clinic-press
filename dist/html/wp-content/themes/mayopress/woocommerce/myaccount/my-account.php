<?php
/**
 * My Account page
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="my-account__header">
	<h1 id="my-account-header"><?php echo _e('My Account', 'mayo'); ?></h1>
	<a id="my-account-header-sign-out" href="<?php echo esc_url( wc_logout_url() ); ?>"><?php echo _e('( sign out )', 'mayo'); ?></a>
</div>

<?php
/**
 * My Account navigation.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_navigation' ); ?>

<div class="woocommerce-MyAccount-content">
	<?php
		/**
		 * My Account content.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_account_content' );
	?>
</div>
