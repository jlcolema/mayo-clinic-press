<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Change number of search results
function modify_search_per_page( $query) {
  if ( $query->is_search() || $query->is_main_query() && ! is_admin() ) {
    if ( wp_is_mobile() ) {
      $query->set( 'posts_per_page', '24' );
    } else {
      $query->set( 'posts_per_page', '48' );
    }
  }
}
add_filter( 'pre_get_posts', 'modify_search_per_page' );

// popular categories by term
function categories_list($terms, $title, $num, $link, $button_text) {
  $orderby = 'count';
  $order = 'desc';
  $hide_empty = false ;
  $cat_args = array(
    'orderby'    => $orderby,
    'order'      => $order,
    'hide_empty' => $hide_empty,
  );

  if ( $terms == 'post_cat' ) {
    $terms = 'category';
    $cat_args['post_type'] = 'post';
  }

  $product_categories = get_terms( $terms, $cat_args );

  if( !empty($product_categories) ){
    $index = 0;
    echo '<div class="popular-cats__container">';
    echo '<h2 class="popular-cats__title">' . $title . '</h2>';
    echo '<ul class="popular-cats__list">';
    foreach ($product_categories as $key => $category) {
      if ($index == $num) {
        break;
      }

      $cat_url = get_term_link($category);
      if ( $terms == 'category' ) {
        $cat_url = rtrim( $cat_url, "/" );
      }


      echo '<li>';
      echo '<a href="'.$cat_url.'" >';
      echo $category->name;
      echo '</a>';
      echo '</li>';
      $index = $index + 1;
    }
    echo '</ul>';
    echo '<a href="' . $link . '" class="btn">' . $button_text . '</a>';
    echo '</div>';
  }
}
 