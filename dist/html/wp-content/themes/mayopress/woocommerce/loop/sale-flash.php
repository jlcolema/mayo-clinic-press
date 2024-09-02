<?php
/**
 * Product loop sale flash
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

?>
<?php if ( get_post_meta( $post->ID, '_wc_pre_orders_enabled', true ) == 'yes' ) : ?>

  <?php if ( $product->is_on_sale() ) : ?>

    <?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'SALE', 'woocommerce' ) . '</span>', $post, $product ); ?>

  <?php endif; ?>

	<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale preorder">' . esc_html__( 'PRE-ORDER', 'woocommerce' ) . '</span>', $post, $product ); ?>

<?php elseif ( has_term( 'new-release', 'badge-tags', $post ) ) : ?>

  <?php if ( $product->is_on_sale() ) : ?>

    <?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'SALE', 'woocommerce' ) . '</span>', $post, $product ); ?>

  <?php endif; ?>

  <?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale newrelease">' . esc_html__( 'NEW RELEASE', 'woocommerce' ) . '</span>', $post, $product ); ?>

<?php elseif ( $product->is_on_sale() ) : ?>

	<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'SALE', 'woocommerce' ) . '</span>', $post, $product ); ?>

<?php endif;