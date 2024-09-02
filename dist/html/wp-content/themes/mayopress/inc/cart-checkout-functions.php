<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CHECKOUT PAGE
 */
add_action( 'woocommerce_checkout_order_summary', 'woocommerce_order_review', 10 );
add_action( 'woocommerce_checkout_order_payment', 'woocommerce_checkout_payment', 20 );

function format_phone($phone) {
  $phone = preg_replace("/[^0-9]/", "", $phone);

  if(strlen($phone) == 10)
      return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
  else if(strlen($phone) == 11)
      return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3-$4", $phone);
  else return false;
}

// stop force authentication plugin from redirecting to account login with guest checkout
if ( isset( $_GET[ 'guest_checkout' ] ) || isset( $_GET[ 'key' ] ) ) {
  add_filter( 'wc_force_auth_redirect_to_account_page', false );
  add_filter( 'woocommerce_checkout_fields' , 'checkout_password_fields' );
  
  function checkout_password_fields( $fields ) {
    $fields['account']['account_password']['label'] = 'Add a password to create an account';
    $fields['account']['account_password']['required'] = false;
    $fields['account']['account_password']['placeholder'] = '';

    return $fields;
  }
}

if ( get_option( 'woocommerce_enable_guest_checkout' ) == 'yes' ) {
  remove_action( 'wp_head', array( WC_Force_Auth_Before_Checkout::get_instance(), 'add_wc_notice' ) );
}

add_filter( 'woocommerce_cart_needs_shipping_address', 'custom_cart_needs_shipping_address', 100, 1 );
function custom_cart_needs_shipping_address( $needs_shipping_address ) {
	$cart_contents = WC()->cart->get_cart();
	foreach ( $cart_contents as $cart_item_key => $cart_item ) {
		$product = $cart_item['data'];

		// Check if product is not a subscription product.
		if ( ! $product->is_type( array( 'subscription', 'variable-subscription', 'subscription_variation' ) ) ) {
			return true;
		}
	}

	return false;
}

function cart_contains_subscription() {
  if ( class_exists( 'WC_Subscriptions_Cart' ) ) {
    return WC_Subscriptions_Cart::cart_contains_subscription();
  }
  return false;
}

add_filter( 'woocommerce_checkout_fields' , 'account_fields_required_with_subscription' );
function account_fields_required_with_subscription( $fields ) {
  if ( cart_contains_subscription() ) {
    foreach ( $fields['account'] as $key => $field ) {
      $fields['account'][$key]['required'] = true;
    }
  }
  return $fields;
}


/**
 * CART PAGE
 */
add_filter( 'woocommerce_cart_item_price', 'change_cart_price_display', 30, 3 );

function change_cart_price_display( $price, $values, $cart_item_key ) {
   $slashed_price = $values['data']->get_price_html();
   $is_on_sale = $values['data']->is_on_sale();
   if ( $is_on_sale ) {
      $price = $slashed_price;
   }
   return $price;
}

/**
 * Mobile Cart View
 */

if ( ! function_exists( 'woocommerce_cart_totals_two' ) ) {

	/**
	 * Output the cart totals.
	 */
	function woocommerce_cart_totals_two() {
		if ( is_checkout() ) {
			return;
		}
		wc_get_template( 'cart/cart-totals-two.php' );
	}
}

/**
 * Cross Sell Slider View
 */

if ( ! function_exists( 'woocommerce_template_loop_product_title_cross' ) ) {

	/**
	 * Show the product title in the product loop. By default this is an H2.
	 */
	function woocommerce_template_loop_product_title_cross() {
		echo '<div class="title-price"><h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Cross Sell Title View
 */

if ( ! function_exists( 'woocommerce_template_loop_price_cross' ) ) {

	/**
	 * Get the product price for the loop.
	 */
	function woocommerce_template_loop_price_cross() {
		wc_get_template( 'loop/price-cross.php' );
	}
}

/**
 * Gets recurring total html including inc tax if needed.
 *
 * @param WC_Cart The cart to display the total for.
 */
function wcs_cart_totals_order_total_html_new( $cart ) {
	$order_total_html           = 'This order includes a recurring payment of <strong>' . $cart->get_total() . ' ';
	$tax_total_html             = '';
	$display_prices_include_tax = wcs_is_woocommerce_pre( '3.3' ) ? ( 'incl' === $cart->tax_display_cart ) : $cart->display_prices_including_tax();

	// If prices are tax inclusive, show taxes here
	if ( wc_tax_enabled() && $display_prices_include_tax ) {
		$tax_string_array = array();
		$cart_taxes       = $cart->get_tax_totals();

		if ( get_option( 'woocommerce_tax_total_display' ) === 'itemized' ) {
			foreach ( $cart_taxes as $tax ) {
				$tax_string_array[] = sprintf( '%s %s', $tax->formatted_amount, $tax->label );
			}
		} elseif ( ! empty( $cart_taxes ) ) {
			$tax_string_array[] = sprintf( '%s %s', wc_price( $cart->get_taxes_total( true, true ) ), WC()->countries->tax_or_vat() );
		}

		if ( ! empty( $tax_string_array ) ) {
			// translators: placeholder is price string, denotes tax included in cart/order total
			$tax_total_html = '<small class="includes_tax"> ' . sprintf( _x( '(includes %s)', 'includes tax', 'woocommerce-subscriptions' ), implode( ', ', $tax_string_array ) ) . '</small>';
		}
	}

	// Apply WooCommerce core filter
	$order_total_html = apply_filters( 'woocommerce_cart_totals_order_total_html', $order_total_html );

	echo wp_kses_post( apply_filters( 'wcs_cart_totals_order_total_html_new', wcs_cart_price_string( $order_total_html, $cart ) . $tax_total_html, $cart ) );
}

/**
 * Append the first renewal payment date to a string (which is the order total HTML string by default)
 *
 * @access public
 * @return string
 */
function wcs_add_cart_first_renewal_payment_date_new( $order_total_html, $cart ) {

	if ( 0 !== $cart->next_payment_date ) {
		$first_renewal_date = date_i18n( wc_date_format(), wcs_date_to_time( get_date_from_gmt( $cart->next_payment_date ) ) );
		// translators: placeholder is a date
		$order_total_html .= '.</strong> ' . sprintf( __( 'First renewal payment will be <span class="renewal-date"><strong> %s', 'woocommerce-subscriptions' ), $first_renewal_date ) . '</strong></span>';
	}

	return $order_total_html;
}
add_filter( 'wcs_cart_totals_order_total_html_new', 'wcs_add_cart_first_renewal_payment_date_new', 10, 2 );

add_action( 'woocommerce_cart_summary', 'woocommerce_cart_totals', 10 );
add_action( 'woocommerce_cart_summary_two', 'woocommerce_cart_totals_two', 10 );
add_action( 'woocommerce_cart_cross_sell', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_shop_loop_item_title_cross', 'woocommerce_template_loop_product_title_cross', 10 );
add_action( 'woocommerce_after_shop_loop_item_title_cross', 'woocommerce_template_loop_price_cross', 10 );

// co delivery fee
add_action( 'woocommerce_cart_calculate_fees', 'co_delivery_fee' ); 
function co_delivery_fee() {
  $state_fee = array('CO');
  if( in_array( WC()->customer->shipping_state, $state_fee ) ) {
  $surcharge = 0.27; // .27 surcharge for delivery fee
  WC()->cart->add_fee( __('Colorado Delivery Fee', 'woocommerce'), $surcharge );
  }
}
