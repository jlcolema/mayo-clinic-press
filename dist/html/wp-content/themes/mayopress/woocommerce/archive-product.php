<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

$view = 'grid';
if ( get_query_var( 'view' ) && get_query_var ( 'view' ) === 'list' ) {
	$view = 'list';
}

$context              		      = Timber::context();
$context['posts']			          = new Timber\PostQuery();

Timber::render('templates/header.twig', $context);

$shop_title = '' === get_option( 'woocommerce_shop_name' ) ? woocommerce_page_title( false ) : get_option( 'woocommerce_shop_name');

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>
<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php echo $shop_title; ?></h1>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</header>

<div class="product-listing__container">
	<div class="flyout--overlay flyout__button--close"></div>
	<div class="flyout-sidebar__close">
		<a href="javascript:void(0);" class="btn btn--primary flyout__button--close">Close Filters</a>
	</div>
	<?php
	Timber::render('templates/sidebar.twig', array('sidebar' => 'Product facets widget area', 'attributes' => 'data-animation="on" data-component="accordion"', 'class_list' => 'flyout__container accordion js-accordion'));
	?>

	<div class="facetwp-template__container <?php echo $view; ?>">
	<?php
	if ( woocommerce_product_loop() ) {
		// start div.facetwp-template__row--header
		echo '<div class="facetwp-template__row--header">';

		echo '<div class="facetwp-flyout__container is-hidden--desktop" data-component="mobile-flyout">
			<div class="flyout__button--open flyout__button--sort" data-container="product-listing__container">
				<svg class="icon icon--filter-alt" aria-hidden="true" focusable="false"><use xlink:href="#filter-alt"></use></svg>
				Sort & Filter
			</div>
		</div>';

		/**
		 * Hook: woocommerce_before_shop_loop.
		 *
		 * @hooked woocommerce_output_all_notices - 10
		 * @hooked woocommerce_facetwp_result_count - 20
		 * @hooked woocommerce_add_view_toggle - 30
		 */
		do_action( 'woocommerce_before_shop_loop' );

		// add facetwp sort
		echo '<div class="sort__container is-hidden--tablet-down" data-component="select-format">';
		echo '<p class="view__label">SORT BY:</p>';
		echo facetwp_display('facet','sort_');
    echo '</label>';
		echo '<div class="sort__select--arrows">';
		echo '<svg class="icon icon--chevron-up" aria-hidden="true" focusable="false"><use xlink:href="#chevron-up"></use></svg>';
		echo '<svg class="icon icon--chevron-down" aria-hidden="true" focusable="false"><use xlink:href="#chevron-down"></use></svg>';
		echo '</div>';
		echo '</div>';

		// end div.facetwp-template__row--header
		echo '</div>';

		// add results for mobile
		echo '<div class="is-hidden--desktop facets--selected" data-component="fwp-filters">';
		echo do_shortcode('[facetwp selections="true"]');
		echo '<p><span class="facets--selected-count"></span><a href="javascript:void(0);" onclick="FWP.reset()">Clear All</a></p>';
		echo '</div>';

		// start div.facetwp-template
		echo '<div class="facetwp-template">';

		woocommerce_product_loop_start();

		if ( wc_get_loop_prop( 'total' ) ) {
			$index = 0;
			while ( have_posts() ) {
				the_post();

				/**
				 * Hook: woocommerce_shop_loop.
				 */
				do_action( 'woocommerce_shop_loop' );

				if ( is_shop() && $index === 6 && '' !== get_option( 'woocommerce_plp_banner_text' )) {
					do_action ( 'plp_banner' );
				}

				wc_get_template_part( 'content', 'product' );

				$index = $index + 1;
			}
		}

		woocommerce_product_loop_end();

		// end div.facetwp-template
		echo '</div>';

		// start div.facetwp-template__row--footer
		echo '<div class="facetwp-template__row--footer">';

		/**
		 * Hook: woocommerce_after_shop_loop.
		 *
		 * @hooked woocommerce_pagination - 10
		 * @hooked woocommerce_facetwp_pagination - 20
		 */
		do_action( 'woocommerce_after_shop_loop' );

		// end div.facetwp-template__row--footer
		echo '</div>';
	} else {
		/**
		 * Hook: woocommerce_no_products_found.
		 *
		 * @hooked wc_no_products_found - 10
		 */
		do_action( 'woocommerce_no_products_found' );
	}; ?>
	</div>
</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

Timber::render('templates/footer.twig', $context);
