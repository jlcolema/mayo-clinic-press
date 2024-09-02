<?php
/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<div class="product__quantity--container accordion js-accordion" data-animation="on" data-component="accordion">
		<div class="accordion__item js-accordion__item">
			<div class="accordion__header reset js-accordion__trigger js-tab-focus">
				<svg class="icon icon--info" aria-hidden="true" focusable="false"><use xlink:href="#info"></use></svg>
				<?php echo __( 'Interested in additional annual subscriptions?', 'mayo' ); ?>
			</div>

			<div class="accordion__panel js-accordion__panel">
				<p>
					<?php
					echo 'Thank you for your interest in our Health Letter subscription! While you are welcome to purchase multiple copies of our Health Letter, please note that multiple purchases within the same order will be sent to the same recipient.  If you wish to send a copy to multiple recipients, please complete an order for each individually.';
					echo '<br><br>';
					echo 'If you are looking to recieve multiple copies to the same recipient, you may add them below:';
					?>
				</p>
				<div class="product__quantity">
					<p class="product__quantity--label"><?php echo __( 'Quantity', 'mayo' ); ?></p>
					<div class="quantity__full--container"
						data-price="<?php echo $product->get_price(); ?>"
						data-min="<?php echo $product->get_min_purchase_quantity(); ?>"
						data-max="<?php echo $product->get_max_purchase_quantity(); ?>"
						data-variant="true"
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
			</div>
		</div>
	</div>

	<?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

	<?php if ($product->is_type('subscription') || $product->is_type('variable-subscription')) {
		do_action( 'subscription_before_buy_buttons' );
	 }; ?>

	<div class="product__buttons--container" data-component="wishlist-dropdown">
		<button type="submit" class="single_add_to_cart_button btn btn--primary"><?php echo __( 'Add to Cart', 'mayo'); ?><span class="qty--price"></span></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</div>

	<?php if ($product->is_type('subscription') || $product->is_type('variable-subscription')) {
		do_action( 'subscription_after_buy_buttons' );
	}; ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>
