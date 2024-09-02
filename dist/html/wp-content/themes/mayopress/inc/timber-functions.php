<?php
/**
 * Adds additional data to the main Timber context.
 *
 * @param array $context Timber context.
 *
 * @return array $context Timber context.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function add_to_context($context) {
  $custom_logo_url = wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full');
  $footer_logo_url = wp_get_attachment_image_url(get_theme_mod('footer_logo'), 'medium');

	$context['header_menu'] = new \Timber\Menu('header_menu');
	$context['supplemental_drawer_menu'] = new \Timber\Menu('Supplemental Drawer Menu');
	$context['account_menu'] = new \Timber\Menu('account_menu');
	$context['footer_menu'] = new \Timber\Menu('Footer Menu');
  $context['logout_url'] = esc_url( wc_logout_url() );

	$context['footer_widgets'] = Timber::get_widgets('footer');
  $context['banner_options'] = get_fields('banner_options');
  $context['blog_banner_options'] = get_fields('blog_banner_options');
	if (!is_404()) {
		$context['sidebar'] = Timber::get_widgets('sidebar');
	}

  // address information
  $context['store_name']        = get_bloginfo( 'name' );
  $context['store_address']     = get_option( 'woocommerce_store_address' );
  $context['store_address_2']   = get_option( 'woocommerce_store_address_2' );
  $context['store_city']        = get_option( 'woocommerce_store_city' );
  $context['store_postcode']    = get_option( 'woocommerce_store_postcode' );

  // Split the country/state
  $split_country                = explode( ":", get_option( 'woocommerce_default_country' ) );
  $context['store_country']     = $split_country[0] == 'US' ? 'USA' : $split_country[0];
  $context['store_state']       = $split_country[1];

  $context['custom_logo_url'] = $custom_logo_url;
  $context['footer_logo_url'] = $footer_logo_url;
  $context['render_widget_area'] = render_widget_area($context);

  $context['cart_url'] = wc_get_cart_url();
  $context['logout_url'] = esc_url(wc_logout_url());

  // cart count
  if ( WC() && WC()->cart ) {
    $context['cart_count'] = WC()->cart->get_cart_contents_count();
  }

  // aglolia session results
  if ( WC() && WC()->session ) {
    $context['cachedProducts'] = WC()->session->get('user_algolia_products');
    $context['cachedPosts'] = WC()->session->get('user_algolia_posts');
  }

  // get domain
  $current_url = Timber\URLHelper::get_current_url();
  $url_components = parse_url($current_url);

  if (isset($url_components['scheme']) && isset($url_components['host'])) {
    $origin = $url_components['scheme'] . ':%2F%2F' . $url_components['host'];
  } else {
    $origin = '';
  }

  $context['origin'] = $origin;

  // create pages and categories context for all-topics and navigation
  $context['pages']     = get_pages();
  $standard_categories_ids = [322, 323, 324, 325, 326, 255, 327, 295, 328, 319, 259, 329, 317, 257];

  $categories = get_categories( array(
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => '1',
    'include' => $standard_categories_ids,
  ) );
  $context['categories'] = $categories;

  $promoted_categories = get_categories(array(
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => '1',
    'exclude' => array_merge([1], $standard_categories_ids),
  ));
  $context['promoted_categories'] = $promoted_categories;

	return $context;
}

add_filter('timber/context', 'add_to_context');

// set timber post for products
function timber_set_product( $post ) {
  global $product;

  $product = wc_get_product( $post->ID );
}
