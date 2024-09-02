<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) : ?>

	<div class="cross-sells" data-component="cross-sell">
		<?php
		$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You May Also Like', 'woocommerce' ) );

		if ( $heading ) :
			?>
      <div class="woo-cart--heading">
			  <h2><?php echo esc_html( $heading ); ?></h2>
      </div>
		<?php endif; ?>

    <div class="carousel">
      <div class="nav nav-left">
        <div class="ion-chevron-left carousel-arrow-icon-left"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m22.29 30.71-14.7-14.71 14.7-14.71 1.42 1.42-13.3 13.29 13.3 13.29z"/></svg></div>
      </div>
      <ul class="carousel-content">

        <?php foreach ( $cross_sells as $cross_sell ) : ?>

          <?php
            $post_object = get_post( $cross_sell->get_id() );

            setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

            wc_get_template_part( 'content', 'cross-sell' );
          ?>

        <?php endforeach; ?>

      </ul>
      <div class="nav nav-right">
        <div class="ion-chevron-right carousel-arrow-icon-right"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m9.71 30.71-1.42-1.42 13.3-13.29-13.3-13.29 1.42-1.42 14.7 14.71z"/></svg></div>
      </div>
    </div>

	</div>
	<?php
endif;

wp_reset_postdata();
