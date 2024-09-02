<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// modify product categories
function category_canonical( $canonical ) {
  if ( is_shop() ) {
    $cat = get_query_var( '_categories' );
    if ($cat) {
      $canonical = 'https://mcpress.mayoclinic.org/product-category/' . $cat . '/';
    }
	}

  return $canonical;
}
add_filter( 'wpseo_canonical', 'category_canonical', 100 );

// rewrite categories
function rewrite_categories() {
  $categories = get_categories();
  foreach($categories as $category) {
    // Get the slug of the category
    $slug = $category->slug;
    add_rewrite_rule(
      '^' . $slug . '/?$',
      'index.php?pagename=' . $slug,
      'top'
    );  
  }
}
add_action( 'init', 'rewrite_categories' );
