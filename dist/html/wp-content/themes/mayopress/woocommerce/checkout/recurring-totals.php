<?php
/**
 * Recurring totals
 *
 * @author  Prospress
 * @package WooCommerce Subscriptions/Templates
 * @version 3.1.0
 * @deprecated $carts_with_multiple_payments. This variable is no longer used to set row spans. It is now dynamically generated by the number of rows displayed.
 * @deprecated $shipping_methods. This variable is no longer used.
 */

defined( 'ABSPATH' ) || exit;
$display_th = true;
// do not display if manual renewal is required
if( !wcs_is_manual_renewal_required() ) :
?>

<div class="recurring-totals">



<?php

/**
 * @since 3.1.0
 * @hooked WCS_Template_Loader::get_recurring_cart_subtotals() Shows the recurring subtotals table rows.
 */
//do_action( 'woocommerce_subscriptions_recurring_totals_subtotals', $recurring_carts );

/**
 * @since 3.1.0
 * @hooked WCS_Template_Loader::get_recurring_cart_coupons() Shows the recurring coupons table rows.
 */
//do_action( 'woocommerce_subscriptions_recurring_totals_coupons', $recurring_carts );

/**
 * @since 3.1.0
 * @hooked WCS_Template_Loader::get_recurring_cart_shipping() Shows the recurring shipping totals table rows.
 */
//do_action( 'woocommerce_subscriptions_recurring_totals_shipping', $recurring_carts );

/**
 * @since 3.1.0
 * @hooked WCS_Template_Loader::get_recurring_cart_fees() Shows the recurring fee totals table rows.
 */
//do_action( 'woocommerce_subscriptions_recurring_totals_fees', $recurring_carts );

/**
 * @since 3.1.0
 * @hooked WCS_Template_Loader::get_recurring_cart_taxes() Shows the recurring tax totals table rows.
 */
//do_action( 'woocommerce_subscriptions_recurring_totals_taxes', $recurring_carts );

/**
 * @since 3.1.0
 * @hooked WCS_Template_Loader::get_recurring_subscription_totals() Shows the recurring subscription totals table rows.
 */
do_action( 'woocommerce_subscriptions_recurring_subscription_totals', $recurring_carts );
?>
</div>

<?php
endif;
