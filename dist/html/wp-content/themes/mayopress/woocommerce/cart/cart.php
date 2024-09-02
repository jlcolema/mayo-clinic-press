<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;
global $woocommerce;

$context     		 = Timber::context();

$text_serif = get_field('cart_banner_serif', 'banner_options');
$text_sans = get_field('cart_banner_sans', 'banner_options');
$button_text = get_field('cart_banner_button', 'banner_options');
$button_link = get_field('cart_button_link', 'banner_options');
$has_digital_sub = false;

do_action( 'woocommerce_before_cart' );
?>

<div class="woo-cart--heading"><h2>Cart</h2><span>(<?php echo $woocommerce->cart->cart_contents_count; ?> items)</span><span class="cart-msg is-hidden"><?php do_action( 'woocommerce_before_cart' ); ?></span></div>
<div class="mobile-extra-checkout is-hidden--desktop">
  <?php do_action( 'woocommerce_cart_summary_two' ); ?>
</div>
<div class="woo-cart__container">
  <div class="woo-cart__left">
    <div class="woo-cart-items" data-component="cart-sale">
      <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
        <?php do_action( 'woocommerce_before_cart_table' ); ?>
        <?php do_action( 'woocommerce_before_cart_contents' ); ?>
        <?php
          foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            // if a digital healthletter is in the cart
            if ( $_product->get_id() == 41814 || $_product->get_id() == 41815 || $_product->get_id() == 47981 ) {
              $has_digital_sub = true;
            } else if ( $_product->get_id() == 41813 ) {
              $text_sans = 'Add digital access to the Mayo Clinic Health Letter for only <strong>$8.47 more/year!</strong>';
            } else if ( $_product->get_id() == 47982 ) {
              $text_sans = 'Add digital access to the Mayo Clinic Health Letter for only <strong>$8.44 more/year!</strong>';
            }

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
              $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
        ?>
        <div class="cart-item">
          <span class="is-hidden--tablet">
            <?php if( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $_product ) && !wcs_is_manual_renewal_required() ) {
              echo '<span class="renewal-title">';
              do_action( 'woocommerce_cart_totals_after_order_total' );
              echo '</span>';
            }; ?>
          </span>
          <div class="item-inner">
            <div class="item-image">
              <div class="img-div">
              <?php
                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                if ( ! $product_permalink ) {
                  echo $thumbnail; // PHPCS: XSS ok.
                } else {
                  printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                }
              ?>
              </div>
            </div>
            <div class="info-container">
              <div class="item-info">
                <div class="info-top">
                  <span class="is-hidden--mobile">
                  <?php if( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $_product ) && !wcs_is_manual_renewal_required() ) {
                    echo '<span class="renewal-title">';
                    do_action( 'woocommerce_cart_totals_after_order_total' );
                    echo '</span>';
                  }; ?>
                  </span>
                  <span class="item-title">
                  <?php
                    if ( ! $product_permalink ) {
                      echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                    } else {
                      echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                    }

                    do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                    // Meta data.
                    echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

                    // Backorder notification.
                    if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                      echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                    }
                    ?>
                  </span>
                  <span class="item-sale">
                    <?php
                      echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                    ?>
                  </span>
                  <div class="subscrpip-qty">
                    <?php if( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $_product ) ) {
                    echo '<details>
                            <summary>Interested in additional annual subscriptions?</summary>
                            <p>Thank you for your interest in our Health Letter subscription!
                            While you are welcome to purchase multiple copies of our Health Letter, please note that multiple purchases within the same order will be sent to the same recipient.  If you wish to send a copy to multiple recipients, please complete an order for each individually.</p><p>If you are looking to recieve multiple copies to the same recipient, you may add them below:</p>';

                          if ( $_product->is_sold_individually() ) {
                          $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                        } else {
                          $product_quantity = woocommerce_quantity_input(
                          array(
                            'input_type'   => 'text',
                            'input_name'   => "cart[{$cart_item_key}][qty]",
                            'input_value'  => $cart_item['quantity'],
                            'max_value'    => $_product->get_max_purchase_quantity(),
                            'min_value'    => '1',
                            'product_name' => $_product->get_name(),
                          ),
                          $_product,
                            false
                          );
                        }

                            echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
                    }
                    ?>
                  </div>
                </div>
                <div class="item-qty-price">
                  <div class="item-price<?php if( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $_product ) ) : ?> item-price__subscription<?php endif; ?>">
                    <?php
                      echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                    ?>
                  </div>

                  <div class="qty-selector">
                    <?php
                      if( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $_product ) ) {

                      } else {
                        if ( $_product->is_sold_individually() ) {
                          $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                        } else {
                          $product_quantity = woocommerce_quantity_input(
                          array(
                            'input_type'   => 'text',
                            'input_name'   => "cart[{$cart_item_key}][qty]",
                            'input_value'  => $cart_item['quantity'],
                            'max_value'    => $_product->get_max_purchase_quantity(),
                            'min_value'    => '1',
                            'product_name' => $_product->get_name(),
                          ),
                          $_product,
                            false
                          );
                        }

                        echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="info-bottom is-hidden--mobile">
                <span class="item-remove">
                  <?php
                    $product_name = $_product->get_name();
                    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                      'woocommerce_cart_item_remove_link',
                      sprintf(
                        '<a href="%s" class="remove-prod" data-product_id="%s" data-product_sku="%s">Remove <span class="screen-reader-text">%s: %s</span></a>',
                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                        esc_attr( $product_id ),
                        esc_attr( $_product->get_sku() ),
                        esc_html__( 'Remove this item', 'woocommerce' ),
                        esc_html( $product_name )
                      ),
                      $cart_item_key
                    );
                  ?>
                </span>
              </div>
            </div>
          </div>
          <div class="info-bottom-mobile is-hidden--tablet">
              <span class="item-remove">
                <?php
                  $product_name = $_product->get_name();
                  echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    'woocommerce_cart_item_remove_link',
                    sprintf(
                      '<a href="%s" class="remove-prod" data-product_id="%s" data-product_sku="%s">Remove <span class="screen-reader-text">%s: %s</span></a>',
                      esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                      esc_attr( $product_id ),
                      esc_attr( $_product->get_sku() ),
                      esc_html__( 'this item', 'woocommerce' ),
                      esc_html( $product_name )
                    ),
                    $cart_item_key
                  );
                ?>
              </span>
            </div>
        </div>
        <?php
            }
          }
        ?>
        <?php do_action( 'woocommerce_cart_contents' ); ?>
        <div class="is-hidden">
          <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>
        </div>
        <?php do_action( 'woocommerce_cart_actions' ); ?>
        <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
      </form>
      <?php do_action( 'woocommerce_after_cart_contents' ); ?>
      <?php do_action( 'woocommerce_after_cart_table' ); ?>
    </div>
    <?php if ( !$has_digital_sub && ( $text_serif || $text_sans ) ): ?>
    <div class="woo-cart-offer">
      <div class="offer-left">
        <?php if ( $text_serif ): ?>
        <span class="offer-heading"><?php echo wp_kses( $text_serif, array( 'strong' => array() ) ); ?></span>
        <?php endif; ?>

        <?php if ( $text_sans ): ?>
        <span class="offer-text"><?php echo wp_kses( $text_sans, array( 'strong' => array() ) ); ?></span>
        <?php endif; ?>
      </div>

      <?php if ( $button_text || $button_link ): ?>
      <div class="offer-right">
        <a href="<?php echo esc_url( $button_link ); ?>" class="btn"><?php echo esc_html( $button_text ); ?></a>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <div class="woo-cart__right">
    <div class="woo-cart-summary is-hidden--tablet-down">
      <?php do_action( 'woocommerce_cart_summary' ); ?>
    </div>

    <?php Timber::render('template-parts/components/proceeds-banner.twig', $context);	?>
    <?php Timber::render('template-parts/components/shop-confidence.twig', $context);	?>
    <?php Timber::render('template-parts/components/accepted-payments.twig', $context);	?>
  </div>
</div>
<div class="woo-cart-also-like"><?php do_action( 'woocommerce_cart_cross_sell' );?></div>



<?php do_action( 'woocommerce_after_cart' ); ?>
<script>
  const wacButtons = document.querySelectorAll('a.wac-qty-button');
  if (wacButtons.length > 0) {
    wacButtons.forEach(item => {
      item.textContent = '';
    });
  }
  const subscript = document.querySelectorAll('.item-sale .subscription-details');
  if (subscript.length > 0) {    
    subscript.forEach(item => {
      item.parentNode.insertBefore(item, item.previousElementSibling);
    });
  }
  const renewTitle = document.querySelectorAll('.renewal-title')
  if (renewTitle.length > 0) {
    renewTitle.forEach(item => {
      const renewDate = item.querySelector('.renewal-date');
      if (renewDate) {
        item.innerHTML = `<strong>First Renewal:&nbsp</strong> ` + renewDate.textContent;
      }
    });
  }
</script>
