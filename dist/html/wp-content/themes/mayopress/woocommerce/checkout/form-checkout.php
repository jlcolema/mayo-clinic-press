<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $recurring_carts;

$context     		 = Timber::context();

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

<div class="woo-checkout--heading">
  <h2>Secure Checkout</h2>
</div>
<div class="woo-checkout-container" data-component="checkout-details">
  <span class="cart-msg">
    <div class="woocommerce-notices-wrapper">
      <?php do_action( 'woocommerce_before_checkout_form', $checkout );
        // If checkout registration is disabled and not logged in, the user cannot checkout.
        if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
          echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'mayo' ) ) );
          return;
        }
      ?>
    </div>
  </span>

  <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
    <div class="woo-order-review is-hidden--desktop">
      <div class="accordion js-accordion" data-animation="on" data-component="accordion">
        <div class="accordion__item js-accordion__item">
          <div class="checkout-summary-heading js-accordion__trigger js-tab-focus">
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

            <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

            <div class="woocommerce-checkout-review-order">
              <?php do_action( 'woocommerce_checkout_order_summary' ); ?>
              <?php if ( class_exists( 'WC_Subscriptions_Cart' ) && ( $recurring_carts = WC_Subscriptions_Cart::display_recurring_totals() ) ) : ?>
                <?php foreach ( $recurring_carts as $recurring_cart_key => $recurring_cart ) : ?>
                  <?php wcs_cart_totals_order_total_html_new( $recurring_cart ); ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

            <div class="mobile-pay-btn">
              <button disabled="" type="submit" class="btn btn--primary" name="woocommerce_checkout_place_order" id="place_order_holder" value="Place order" data-value="Continue to Payment">Continue to Payment</button>
            </div>

            <?php Timber::render('template-parts/components/proceeds-banner.twig', $context);	?>
            <?php Timber::render('template-parts/components/shop-confidence.twig', $context);	?>
            <?php Timber::render('template-parts/components/accepted-payments.twig', $context);	?>
          </div>
        </div>
      </div>
    </div>

    <div class="customer-summary">
      <div class="customer-summary__row">
        <p>
          <strong><?php echo __('Contact', 'mayo'); ?></strong>
          <a href="javascript:void(0);" class="btn--to-shipping-address change">
            <?php echo __('Change', 'mayo'); ?>
            <span class="sr-only">
              <?php echo __('contact', 'mayo'); ?>
            </span>
          </a>
        </p>
        <div>
          <p class="customer__name"></p>
          <p class="customer__email"></p>
        </div>
      </div>
      <div class="customer-summary__row">
        <p>
          <strong><?php echo __('Shipping Address', 'mayo'); ?></strong>
          <a href="javascript:void(0);" class="btn--to-shipping-address change">
            <?php echo __('Change', 'mayo'); ?>
            <span class="sr-only">
              <?php echo __('shipping address', 'mayo'); ?>
            </span>
          </a>
        </p>
        <div>
          <p class="customer__address"></p>
        </div>
      </div>
      <div class="customer-summary__row">
        <p>
          <strong><?php echo __('Billing Address', 'mayo'); ?></strong>
          <a href="javascript:void(0);" class="btn--to-shipping-address change to--billing">
            <?php echo __('Change', 'mayo'); ?>
            <span class="sr-only">
              <?php echo __('billing address', 'mayo'); ?>
            </span>
          </a>
        </p>
        <div>
          <p class="customer__billing"></p>
        </div>
      </div>
      <div class="row__shipping-method">
        <div class="customer-summary__row">
          <p>
            <strong><?php echo __('Shipping Method', 'mayo'); ?></strong>
            <a href="javascript:void(0);" class="btn--to-shipping-method change">
              <?php echo __('Change', 'mayo'); ?>
              <span class="sr-only">
                <?php echo __('shipping method', 'mayo'); ?>
              </span>
            </a>
          </p>
          <div class="customer__method"></div>
        </div>
      </div>
      <?php if ( ! is_user_logged_in() ) : ?>
        <div class="customer-summary__row">
          <p>
            <strong><?php echo __('Create Account', 'mayo'); ?></strong>
            <a href="javascript:void(0);" class="btn--to-shipping-address change">
              <?php echo __('Change', 'mayo'); ?>
              <span class="sr-only">
                <?php echo __('create account', 'mayo'); ?>
              </span>
            </a>
          </p>
          <div>
            <p class="customer__create"></p>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="woo-checkout-mid">
      <div class="woo-checkout-left">
        <?php if ( $checkout->get_checkout_fields() ) : ?>

        <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

        <div id="customer_details">
          <div class="woo-shipping">
            <h2>Shipping Address</h2>
            <?php do_action( 'woocommerce_checkout_shipping' ); ?>
          </div>

          <div class="woo-billing">
            <div id="ship-to-different-address">
              <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" /> <span><?php esc_html_e( 'Billing address is different from shipping address', 'mayo' ); ?></span>
              </label>
            </div>

            <h2>Billing Address</h2>
            <?php do_action( 'woocommerce_checkout_billing' ); ?>

            <div role="button" class="btn btn--primary btn--to-shipping-method"><?php echo __('Continue to Shipping Method', 'mayo'); ?></div>
          </div>
        </div>

        <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

        <div class="shipping-options__container">
          <div class="steps-header">
            <p><strong><?php echo __('Shipping Method', 'mayo'); ?></strong></p>
          </div>
          <h2><?php echo __('Shipping Method', 'mayo'); ?></h2>
          <div class="loading-spinner"></div>
          <div class="shipping-options is-invisible">
            <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

            <?php wc_cart_totals_shipping_html(); ?>

            <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

            <div role="button" class="btn btn--primary btn--to-review" disabled=""><?php echo __('Continue to Order Review', 'mayo'); ?></div>
          </div>
        </div>

        <?php endif; ?>
      </div>

      <div class="woo-checkout-right">
        <div class="woo-order-review is-hidden--tablet-down">
          <h3 class="checkout-summary-heading"><?php echo __('Order Summary', 'woocommerce'); ?></h3>

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

          <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

          <div id="order_review" class="woocommerce-checkout-review-order">
            <?php do_action( 'woocommerce_checkout_order_summary' ); ?>
            <?php if ( class_exists( 'WC_Subscriptions_Cart' ) && ( $recurring_carts = WC_Subscriptions_Cart::display_recurring_totals() ) ) : ?>
              <?php foreach ( $recurring_carts as $recurring_cart_key => $recurring_cart ) : ?>
                <?php wcs_cart_totals_order_total_html_new( $recurring_cart ); ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
        </div>

        <?php Timber::render('template-parts/components/proceeds-banner.twig', $context);	?>
        <?php Timber::render('template-parts/components/shop-confidence.twig', $context);	?>
        <?php Timber::render('template-parts/components/accepted-payments.twig', $context);	?>
      </div>

      <div class="woo-checkout-bottom">
        <div class="payment__container">
          <div class="steps-header">
            <p><strong><?php echo __('Payment', 'mayo'); ?></strong></p>
          </div>
          <div class="payment">
            <div class="email__optin" data-component="email-optin" data-target="email__optin--form">
              <label class="email__optin--label" for="email-optin">
                <input id="email-optin" class="email__optin--checkbox input-checkbox" type="checkbox" name="email-optin">
                <p>
                  Sign up for our free newsletter and stay up to date on research advancements, health tips and more!

                  <a class="email-capture__modal--link" href="javascript:void(0);" aria-describedby="optin-additional-info-modal" aria-controls="optin-additional-information">
                    Learn More about the Mayo Clinic’s use of data
                    <span id="optin-additional-info-modal" class="sr-only">Open additional information modal</span>
                  </a>
                </p>
              </label>

              <div id="optin-additional-information" class="modal modal--animate-scale js-modal" data-component="modal">
                <div class="modal__content" role="alertdialog" aria-labelledby="optin-additional-information" aria-describedby="optin-additional-information">
                  <p>To provide you with the most relevant and helpful information, and understand which information is beneficial, we may combine your email and website usage information with other information we have about you. If you are a Mayo Clinic patient, this could include protected health information. If we combine this information with your protected health information, we will treat all of that information as protected health information and will only use or disclose that information as set forth in our notice of privacy practices. You may opt-out of email communications at any time by clicking on the unsubscribe link in the e-mail.</p>
                  <a href="javascript:void(0);" class="reset modal__close-btn modal__close-btn--text js-modal__close"><?php echo __('Close', 'mayo'); ?></a>
                </div>
              </div>
            </div>
            <h2><?php echo __('Payment', 'mayo'); ?></h2>
            <?php do_action( 'woocommerce_checkout_order_payment' ); ?>
          </div>
        </div>
      </div>
    </div>
  </form>
  <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
  <div class="email__optin--form is-hidden">
    <?php echo do_shortcode('[contact-form-7 id="61169" title="Email Capture"]'); ?>
  </div>
</div>

