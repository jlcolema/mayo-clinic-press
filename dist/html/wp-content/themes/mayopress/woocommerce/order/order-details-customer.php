<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.4.4
 */

 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<header><h4 class="woocommerce-order-details__title"><?php esc_html_e( 'Customer Details', 'mayo' ); ?></h4></header>


<div class="customer_details">
    <div class="customer-detail-col">
        <?php if ( $order->get_customer_note() ) : ?>
            <h4 class="customer-detail-header"><?php esc_html_e( 'Note:', 'mayo' ); ?></h4>
            <p class="customer-detail-text"><?php echo wptexturize( $order->get_customer_note() ); ?></p>
        <?php endif; ?>
        <h4 class="customer-detail-header"><?php esc_html_e( 'Billing Address', 'mayo' ); ?></h4>
        <p class="customer-detail-text"><?php echo wp_kses_post( $order->get_formatted_billing_address( __( 'N/A', 'mayo' ) ) ); ?></p>

        <?php if ( $show_shipping ) : ?>
            <h4 class="customer-detail-header"><?php esc_html_e( 'Shipping Address', 'mayo' ); ?></h4>
            <p class="customer-detail-text"><?php echo wp_kses_post( $order->get_formatted_shipping_address( __( 'N/A', 'mayo' ) ) ); ?></p>
        <?php endif; ?>
    </div>
    
    <div class="customer-detail-col">
        <?php if ( $order->get_billing_email() ) : ?>
            <h4 class="customer-detail-header"><?php esc_html_e( 'Email:', 'mayo' ); ?><h4>
            <p class="customer-detail-text"><?php echo esc_html( $order->get_billing_email() ); ?></p>
        <?php endif; ?>

        <?php if ( $order->get_billing_phone() ) : ?>
            <h4 class="customer-detail-header"><?php esc_html_e( 'Phone:', 'mayo' ); ?></h4>
            <p class="customer-detail-text"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
        <?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
    </div>
    </div>

