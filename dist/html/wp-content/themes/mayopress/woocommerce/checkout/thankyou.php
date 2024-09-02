<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

$context     		 = Timber::context();

?>

<div class="woocommerce-order">

	<?php if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

    <div class="wc-complete-wrap">
      <?php if ( $order->has_status( 'failed' ) ) : ?>
        <div class="wc-side-column">
          <h4 class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'mayo' ); ?></h4>

          <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
            <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'mayo' ); ?></a>
            <?php if ( is_user_logged_in() ) : ?>
              <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My Account', 'mayo' ); ?></a>
            <?php endif; ?>
          </p>
        </div>

      <?php else : ?>
        <div class="woo-checkout--heading">
          <h2>Order Confirmation</h2>
        </div>

        <div class="thanks-summary is-hidden--desktop">
          <div class="accordion js-accordion" data-animation="on" data-component="accordion">
            <div class="accordion__item js-accordion__item">
              <div class="thanks-summary-header js-accordion__trigger js-tab-focus">
                <?php
                  // Get all items from the order
                  $items = $order->get_items();
                  $total_quantity = 0;

                  // Calculate the total quantity of items
                  foreach ( $items as $item ) {
                    $total_quantity += $item->get_quantity();
                  }

                  // Get the total price of the order
                  $total_price = $order->get_total();
                ?>

                <p class="order-summary__heading">
                  <?php esc_html_e( 'Order Summary', 'woocommerce' ); ?>
                  (<?php echo esc_html( $total_quantity ); ?>)
                </p>
                <?php echo wc_price( $total_price ); ?>
                <svg class="icon icon--chevron-down" aria-hidden="true" focusable="false">
                  <use xlink:href="#chevron-down"></use>
                </svg>
              </div>



              <div class="accordion__panel js-accordion__panel">
                <table class="woo-shop-table order-details">
                  <tbody>
                    <?php
                    $order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
                    do_action( 'woocommerce_order_details_before_order_table_items', $order );

                    foreach ( $order_items as $item_id => $item ) {
                      $product = $item->get_product();

                      wc_get_template(
                        'order/order-details-item.php',
                        array(
                          'order'              => $order,
                          'item_id'            => $item_id,
                          'item'               => $item,
                          'show_purchase_note' => $show_purchase_note,
                          'purchase_note'      => $product ? $product->get_purchase_note() : '',
                          'product'            => $product,
                        )
                      );
                    }

                    //do_action( 'woocommerce_order_details_after_order_table_items', $order );
                    ?>
                  </tbody>
                </table>

                <div class="thanks-summary-totals" data-component="thanks-rows">
                  <?php
                    foreach ( $order->get_order_item_totals() as $key => $total ) {
                      ?>
                        <div class="totals-row">
                          <span scope="row"><?php echo esc_html( $total['label'] ); ?></span>
                          <span class="row-amount"><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                    </div>
                        <?php
                    }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="thanks-container">
          <div class="thanks-left">
            <div class="thanks-details">
              <h2>Thank you for your purchase!</h2>
              <p>Please check your inbox for an emailed order receipt.</p>
              <div class="details-container">
                <div class="details-left">
                  <div class="thanks-info">
                    <span class="info-heading">Order number:</span>
                    <?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  </div>
                  <div class="thanks-info">
                    <span class="info-heading">Date:</span>
                    <?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  </div>
                  <div class="thanks-info">
                    <span class="info-heading">Shipping Address</span>
                    <?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
                  </div>
                  <div class="thanks-info">
                    <span class="info-heading">Billing Address:</span>
                    <?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
                  </div>
                </div>
                <div class="details-right">
                  <div class="thanks-info">
                    <span class="info-heading">Total:</span>
                    <?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  </div>
                  <div class="thanks-info">
                    <span class="info-heading">Payment Method:</span>
                    <?php
                      $payment_method_title = $order->get_payment_method_title();
                      // Check if payment method is a credit card
                      if ( strpos( strtolower( $payment_method_title ), 'credit' ) === false ) {
                        echo 'Credit Card';
                      } else {
                        echo wp_kses_post( $order->get_payment_method_title() );
                      }
                    ?>
                  </div>
                  <div class="thanks-info">
                    <span class="info-heading">Email:</span>
                    <?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  </div>
                  <div class="thanks-info">
                    <span class="info-heading">Phone:</span>
                    <?php
                      echo esc_html( format_phone( $order->get_billing_phone() ) );
                    ?>
                  </div>
                </div>
              </div>
              <?php if ( is_user_logged_in ) : ?>
                <a href="/my-account/orders/" class="btn btn--primary thanks-view-all-orders">View All Orders</a>
              <?php else : ?>
                <a href="/my-account/" class="btn btn--primary thanks-view-all-orders">View All Orders</a>
              <?php endif; ?>
            </div>
          </div>
          <div class="thanks-right">
            <div class="thanks-summary is-hidden--tablet-down">
              <h3>Order Summary</h3>
              <table class="woo-shop-table order-details">
                <tbody>
              <?php
              $order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
              do_action( 'woocommerce_order_details_before_order_table_items', $order );

              foreach ( $order_items as $item_id => $item ) {
                $product = $item->get_product();

                wc_get_template(
                  'order/order-details-item.php',
                  array(
                    'order'              => $order,
                    'item_id'            => $item_id,
                    'item'               => $item,
                    'show_purchase_note' => $show_purchase_note,
                    'purchase_note'      => $product ? $product->get_purchase_note() : '',
                    'product'            => $product,
                  )
                );
              }

              //do_action( 'woocommerce_order_details_after_order_table_items', $order );
              ?>
            </tbody>
            </table>
            <div class="thanks-summary-totals" data-component="thanks-rows">
              <?php
                foreach ( $order->get_order_item_totals() as $key => $total ) {
                  ?>
                    <div class="totals-row">
                      <span scope="row"><?php echo esc_html( $total['label'] ); ?></span>
                      <span class="row-amount"><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                </div>
                    <?php
                }
              ?>
            </div>

            </div>

            <?php Timber::render('template-parts/components/proceeds-banner.twig', $context);	?>
            <?php Timber::render('template-parts/components/shop-confidence.twig', $context);	?>
            <?php Timber::render('template-parts/components/accepted-payments.twig', $context);	?>
          </div>
        </div>
		    <?php do_action( 'woocommerce_thankyou_downloads', $order->get_id() ); ?>

      <?php endif; ?>

    </div>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'mayo' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

  <?php
    // Empty cart.
    WC()->cart->empty_cart();
  ?>

</div>

