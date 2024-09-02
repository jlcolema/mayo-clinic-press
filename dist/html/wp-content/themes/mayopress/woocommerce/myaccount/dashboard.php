<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<h2 class="dashboard__title"><?php echo _e('Dashboard', 'mayo'); ?></h2>

<p class="dashboard__subtitle">
	<?php echo _e('Find everything you need to manage your account details and orders.', 'mayo');	?>
</p>

<div class="dashboard__cards">
	<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="dashboard__card">
		<div class="card__icon">
			<svg id="dashboard-shopping-cart" class="icon icon--shopping-cart" aria-hidden="true" focusable="false"><use xlink:href="#shopping-cart"></use></svg>
		</div>

		<p class="card__subtitle"><?php echo _e('VIEW', 'mayo'); ?></p>

		<p class="card__title has-heading-md-font-size"><?php echo _e('Order  History', 'mayo'); ?></p>

		<p class="card__description"><?php echo _e('Check the status of your recent order, or view past orders.', 'mayo'); ?></p>
	</a>
	<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="dashboard__card">
		<div class="card__icon">
			<svg  class="icon icon--directory" aria-hidden="true" focusable="false" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m24 12h2v2h-2z"/><path d="m24 16h2v2h-2z"/><path d="m20 2h-14v4h-2v2h2v2h-2v2h2v2h-2v2h2v2h-2v2h2v2h-2v2h2v6h20v-10h-2v8h-16v-4-2-2-2-2-2-2-2-2-2-2h12 4v2h2v-4z"/><path d="m24 8h2v2h-2z"/></svg>
		</div>

		<p class="card__subtitle"><?php echo _e('MANAGE', 'mayo'); ?></p>

		<p class="card__title has-heading-md-font-size"><?php echo _e('Addresses', 'mayo'); ?></p>

		<p class="card__description"><?php echo _e('Manage your shipping and billing addresses.', 'mayo'); ?></p>
	</a>

	<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="dashboard__card">
		<div class="card__icon">
			<svg id="dashboard-person" class="icon icon--person" aria-hidden="true" focusable="false"><use xlink:href="#person"></use></svg>
		</div>

		<p class="card__subtitle"><?php echo _e('EDIT', 'mayo'); ?></p>

		<p class="card__title has-heading-md-font-size"><?php echo _e('Account Details', 'mayo'); ?></p>

		<p class="card__description"><?php echo _e('Make changes to your account details or password.', 'mayo'); ?></p>
	</a>

	
</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
