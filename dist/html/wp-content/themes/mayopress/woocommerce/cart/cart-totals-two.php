<?php
/**
 * Cart totals Two
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

$shipping_methods = WC()->shipping->get_shipping_methods();
$free_shipping_minimum = null;

foreach ( $shipping_methods as $method ) {
  if ( $method->id === 'sfg_shipping_method' ) {
    if ( isset($method->settings['free_shipping_minimum']) ) {
      $free_shipping_minimum = $method->settings['free_shipping_minimum'];
      break;
    }
  }
}

$subtotal = WC()->cart->get_cart_contents_total();
$cart_count = WC()->cart->get_cart_contents_count();
?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

  <div class="accordion js-accordion" data-animation="on" data-component="accordion">
    <div class="accordion__item js-accordion__item">
      <div class="cart-summary-heading js-accordion__trigger js-tab-focus">
        <p class="order-summary__heading"><?php esc_html_e( 'Order Summary', 'woocommerce' ); ?> <?php echo '(' . $cart_count . ')'; ?></p>
        <?php wc_cart_totals_subtotal_html(); ?>
        <svg class="icon icon--chevron-down" aria-hidden="true" focusable="false">
          <use xlink:href="#chevron-down"></use>
        </svg>
      </div>

      <div class="accordion__panel js-accordion__panel">
        <?php if ( !is_null($free_shipping_minimum) ) : ?>
          <?php
            $difference = floatval($free_shipping_minimum) - floatval($subtotal);
            // truncate to 2 decimals
            $difference = floor($difference * 100) / 100;            
            $width = floatval($subtotal) / floatval($free_shipping_minimum) * 100;
          ?>
          <div class="cart-summary-threshold">
            <?php if ($difference > 0) {
              echo '<p>' . sprintf( __( 'Spend $%s more for FREE shipping', 'mayo' ), $difference ) . '</p>';
            } else {
              echo '<p>' . __( 'You\'re getting FREE shipping', 'mayo' ) . '</p>';
            }; ?>
            <div class="status-bar">
              <div class="status-bar__inner" style="width: <?php echo ($difference > 0) ? $width : 100; ?>%"></div>
            </div>
          </div>
        <?php endif; ?>

        <div class="cart-summary-subtotals">
          <div class="subtotals">
            <span class="subtotal-head"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
            <span class="subtotal-amount"><?php wc_cart_totals_subtotal_html(); ?></span>
          </div>
          <div class="cart-summary-shipping">
            <span class="shipping-head">Shipping</span>
            <span class="shipping-amount<?php if ($difference <= 0) : ?> shipping-amount--free">FREE<?php else : ?>">calculated in checkout<?php endif; ?></span>
          </div>
          <div class="cart-summary-tax">
            <span class="tax-head">Tax</span>
            <span class="tax-amount">calculated in checkout</span>
          </div>
          <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <div class="coupon-codes">
              <span class="coupon-head"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
              <span class="coupon-amount"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
            </div>
          <?php endforeach; ?>
          <div class="cart-summary-coupon">
            <?php if ( wc_coupons_enabled() ) { ?>
              <form class="woocommerce-coupon-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
                <details>
                  <summary> Add Coupon Code</summary>
                  <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="btn" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
                  <?php do_action( 'woocommerce_cart_coupon' ); ?>
                </details>
              </form>
            <?php } ?>
          </div>
          <div class="cart-summary-total">
            <span class="total-head"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
            <span class="total-amount">
              <span class="amount"><?php wc_cart_totals_order_total_html(); ?></span>
              <span class="total-note"><?php echo __( 'to be paid today', 'mayo' ); ?></span>
            </span>
          </div>
          <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="proceed-to-checkout">
    <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
  </div>

  <?php do_action( 'woocommerce_after_cart_totals' ); ?>
</div>
