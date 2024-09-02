<?php
/**
 * My Account navigation
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<div class="account-accordion accordion js-accordion" data-animation="on" data-component="accordion" data-autoclose="true" data-mobile-accordion="">
	<div class="accordion__item js-accordion__item">
    <div class="post-accordion__heading accordion__header reset js-accordion__trigger js-tab-focus">Account Menu
		<svg id="chev-down" class="icon icon--chevron-down" aria-hidden="true" focusable="false"><use xlink:href="#chevron-down"></use></svg>
	</div>

		<div class="accordion__panel js-accordion__panel">
			<nav class="woocommerce-MyAccount-navigation" aria-label="My Account Navigation">
				<ul>
					<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
						<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
							<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>

