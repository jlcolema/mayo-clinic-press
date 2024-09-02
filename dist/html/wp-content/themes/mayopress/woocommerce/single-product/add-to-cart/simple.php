<?php
/**
 * Simple product add to cart
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<div class="product__quantity--container">
			<p class="product__quantity--label"><?php echo __( 'Quantity', 'mayo' ); ?></p>
			<div class="quantity__full--container"
				data-price="<?php echo $product->get_price(); ?>"
				data-min="<?php echo $product->get_min_purchase_quantity(); ?>"
				data-max="<?php echo $product->get_max_purchase_quantity(); ?>"
				data-component="quantity-update">
				<div class="qty--button qty--minus">-</div>
					<?php
					do_action( 'woocommerce_before_add_to_cart_quantity' );

					woocommerce_quantity_input(
						array(
							'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
							'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
							'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
						)
					); ?>
				<div class="qty--button qty--plus">+</div>
			</div>
		</div>
		<?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

		<div class="product__buttons--container" data-component="wishlist-dropdown">

      <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="btn btn--primary"><?php echo esc_html( $product->single_add_to_cart_text() ); ?> - $<span class="qty--price"><?php echo esc_html( $product->get_price() ); ?></span></button>

			<?php
				if (is_user_logged_in()) {
					if (class_exists('WC_Pre_Orders_Product')) {
						if (!WC_Pre_Orders_Product::product_can_be_pre_ordered($product)) :
				?>
					<a href="?buy_now=<?php echo esc_attr($product->get_id()); ?>" class="btn btn--buy-now">Buy Now</a>
				<?php
						endif;
					}
				}
			?>

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

    </div>
	</form>

<?php endif; ?>
