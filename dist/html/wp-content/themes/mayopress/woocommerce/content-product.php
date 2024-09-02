<?php
/**
 * The template for displaying product content within loops
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( '', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 */
	do_action( 'woocommerce_after_shop_loop_item' );

	// start div.item__description-price
	echo '<div class="item__description-price">';

	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	// start div.item__description--container
	echo '<div class="item__description--container">';

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 5
	 * @hooked woocommerce_template_loop_product_title - 10
	 * @hooked woocommerce_template_loop_product_author - 20
	 * @hooked woocommerce_template_loop_product_plp_description - 20
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	// end div.item__description--container
	echo '</div>';

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 */
	do_action( 'woocommerce_after_shop_loop_item' );

	// start div.item__price--container
	echo '<div class="item__price--container">';

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_price - 10
	 * @hooked woocommerce_percentage_on_sale - 20
	 * @hooked woocommerce_template_loop_add_to_cart - 30
	 * @hooked view_details_link - 40
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	// end div.item__price--container
	echo '</div>';

	// end div.item__description-price
	echo '</div>';

	// Add the second Add to Cart button for mobile view.
	echo '<div class="is-hidden--tablet">';
	woocommerce_template_loop_add_to_cart();
	echo '</div>';
	?>
</li>
