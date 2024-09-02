<?php

/**
 * Register sidebars
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */
function mayo_sidebars() {
  $footer_args = array(
		'id'            => 'footer',
		'name'          => __('Footer widget area', 'mayo'),
		'description'   => __('Footer widget area', 'mayo'),
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
		'before_widget' => '<section>',
		'after_widget'  => '</section>',
	);
	register_sidebar($footer_args);

  $product_facets = array(
		'id'            => 'product_facets',
		'name'          => __('Product facets widget area', 'mayo'),
		'description'   => __('Product facets widget area', 'mayo'),
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
		'before_widget' => '<section>',
		'after_widget'  => '</section>',
	);
	register_sidebar($product_facets);

  $search_facets = array(
		'id'            => 'search_facets',
		'name'          => __('Search facets widget area', 'mayo'),
		'description'   => __('Search facets widget area', 'mayo'),
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
		'before_widget' => '<section>',
		'after_widget'  => '</section>',
	);
	register_sidebar($search_facets);
}

add_action('widgets_init', 'mayo_sidebars');

/**
 * Wrap dynamic_sidebar in an output buffer for Timber compatibility.
 *
 * @param String  sidebar  The name of the sidebar
 * @return String
 */
function render_widget_area($sidebar) {
  ob_start();
  dynamic_sidebar($sidebar);
  return ob_get_clean();
}
