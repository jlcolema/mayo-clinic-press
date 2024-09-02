<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

$context     		 = Timber::context();
$context['post'] = new Timber\Post();

$product_terms = wp_get_post_terms( $post->ID, 'product_cat' );
$post_terms = wp_get_post_terms( $post->ID );
$product_tax_query = array('relation' => 'OR');
$post_tax_query = array('relation' => 'OR');
$exclude = array(get_the_id());

// Arrange taxonomy query
if ($post_terms) {
  foreach ($post_terms as $term) {
    $arr = array(
      'taxonomy' => $term->taxonomy,
      'field'    => 'term_id',
      'terms'    => $term->term_id
    );

    $post_tax_query[] = $arr;
  }
}

// Arrange taxonomy query
if ($product_terms) {
  foreach ($product_terms as $term) {
    $arr = array(
      'taxonomy' => $term->taxonomy,
      'field'    => 'term_id',
      'terms'    => $term->term_id
    );

		if ( 'childrens-health-books' == $term->slug ) {
			$exclude[] = 56315;
		}

    $product_tax_query[] = $arr;
  }
}

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?> data-component="product-controls">

	<div class="product is-hidden--desktop">
		<?php
		/**
		 * Hook: mobile_product_title.
		 *
	 	 * @hooked woocommerce_show_product_sale_flash - 3
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_product_author - 7
     * @hooked woocommerce_template_single_price - 8
		 */
		do_action( 'mobile_product_title'); ?>
	</div>

	<div class="product__column--left">
	<?php
	/**
	 * Hook: woocommerce_before_single_product_summary.
	 *
	 * @hooked woocommerce_show_product_images - 20
	 */
	do_action( 'woocommerce_before_single_product_summary' );

	Timber::render('template-parts/components/proceeds-banner.twig', $context);
	?>
	</div>

	<div class="summary entry-summary">
		<?php
		/**
		 * Hook: woocommerce_single_product_summary.
		 *
	 	 * @hooked woocommerce_show_product_sale_flash - 3
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_product_author - 7
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked WC_Structured_Data::generate_product_data() - 60
		 */
		do_action( 'woocommerce_single_product_summary' );

		Timber::render('template-parts/components/proceeds-banner.twig', $context);	?>
	</div>
</div>

<div class="product__description-author-wrapper">

  <div class="product__description--container accordion js-accordion" data-animation="on" data-component="accordion" data-mobile-accordion="" data-autoclose="true">
    <?php Timber::render('template-parts/product/product-description.twig', $context); ?>
    <?php Timber::render('template-parts/product/product-facts.twig', $context); ?>
		<?php Timber::render('template-parts/product/product-adtl-resources.twig', $context); ?>
  </div>
  <?php Timber::render('template-parts/product/product-author.twig', $context);	?>
</div>

<div class="product__reviews accordion js-accordion" data-animation="on" data-component="accordion" data-mobile-accordion="" data-autoclose="true">
  <?php Timber::render('template-parts/product/product-reviews.twig', $context);	?>
</div>

<div class="related__products"><?php
	// Set up post query
	$args = array (
		'post_type'      => 'product',
		'tax_query'      => $product_tax_query,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post__not_in'	 => $exclude,
		'posts_per_page' => 8
	);

	$context['product_query'] = Timber::get_posts($args);

	$subscription_product = false;
	foreach ( $product->get_category_ids() as $catId ) {
		$term = get_term_by( 'id', $catId, 'product_cat', 'ARRAY_A' );
		if ($term['name'] == 'subscribe') {
			$subscription_product = true;
		}
	}
	if ( !$subscription_product ) {
		Timber::render('template-parts/product/related-products.twig', $context);
	};
?></div>

<hr class="is-style-wide wp-block-separator">

<div class="related__posts">
	<?php
		// Set up post query
		$args = array (
			'post_type'      => 'post',
			'tax_query'      => $post_tax_query,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => 8
		);

		$context['post_query'] = Timber::get_posts($args);

		Timber::render('template-parts/product/related-posts.twig', $context); ?>
</div>

<hr class="is-style-wide wp-block-separator">

<div class="product__faqs">
  <h2 class="has-text-align-center heading--lg">Frequently Asked Questions</h2>
  <p class="has-text-align-center">Find the answers to some of our most commonly answered questions below. <br><strong>Have a question that isn't answered here?</strong> Feel free to visit our <a href="/contact-us/">Contact Us</a> page.</p>

	<div id="product__faqs-accordion" class="product__faqs-accordion has-max-width-sm">
    <div class="product__faqs-accordion--section">
      <div class="product__faqs-accordion accordion js-accordion" data-animation="on" data-component="accordion" data-autoclose="true">
				<div class="accordion__item js-accordion__item">
					<div class="post-accordion__heading accordion__header reset js-accordion__trigger js-tab-focus">
            How do I find out when my order is shipped?
          	<svg class="icon accordion__icon-plus no-js:is-hidden" viewBox="0 0 20 20" aria-hidden="true"> <g class="icon__group" fill="none" stroke="currentColor"> <line x1="2" y1="10" x2="18" y2="10"></line> <line x1="10" y1="18" x2="10" y2="2"></line> </g> </svg>
          </div>

          <div class="accordion__panel js-accordion__panel">
            <p>We are committed to processing your order promptly, and most orders will ship within 24 hours. If you order online, you will receive a shipping confirmation notice with tracking information. Please allow 7-10 days to receive orders within the USA. Canadian and International orders may take 18-21 business days for delivery.</p>
          </div>
        </div>

        <div class="accordion__item js-accordion__item">
					<div class="post-accordion__heading accordion__header reset js-accordion__trigger js-tab-focus">
            What is your return policy?
          	<svg class="icon accordion__icon-plus no-js:is-hidden" viewBox="0 0 20 20" aria-hidden="true"> <g class="icon__group" fill="none" stroke="currentColor"> <line x1="2" y1="10" x2="18" y2="10"></line> <line x1="10" y1="18" x2="10" y2="2"></line> </g> </svg>
          </div>

          <div class="accordion__panel js-accordion__panel">
            <p>We want you to be completely satisfied with Mayo Clinic Press product. You may return a product within 30 days and receive a prompt refund. To request a return, please email <a href="mailto:mayoclinicpress@service.mayoclinic.com">mayoclinicpress@service.mayoclinic.com</a>.</p>
          </div>
        </div>

        <div class="accordion__item js-accordion__item">
					<div class="post-accordion__heading accordion__header reset js-accordion__trigger js-tab-focus">
            What if a product is out of stock?
          	<svg class="icon accordion__icon-plus no-js:is-hidden" viewBox="0 0 20 20" aria-hidden="true"> <g class="icon__group" fill="none" stroke="currentColor"> <line x1="2" y1="10" x2="18" y2="10"></line> <line x1="10" y1="18" x2="10" y2="2"></line> </g> </svg>
          </div>

          <div class="accordion__panel js-accordion__panel">
            <p>In the event of a temporary stock shortage, we will generally have the product available to ship within 30 days. In the rare occasion that we are unable to meet that deadline, we will not process your order and will notify you when the product becomes available.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
