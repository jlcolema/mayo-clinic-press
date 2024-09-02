<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function get_featured_products($num_items, $always_show = false) {
  $has_personalized_product = false;
  // Check if the user is logged in
  if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    // Get user meta for selected_categories
    $selected_categories = get_user_meta($user_id, 'selected_categories', true);

    if (!empty($selected_categories)) {
      $category_names = get_product_category_names_from_post_category_ids($selected_categories);

      /**
       * Comment if you do not want product selections from Algolia index
       */
      // $has_personalized_product = algolia_search_products_by_categories($category_names, $num_items);

      /**
       * Uncomment if you want product selections from WordPress database
       */
      // // Query for products in selected categories
      // $category_slugs = array();
      // foreach ($selected_categories as $cat_id) {
      //   $category = get_term_by('id', $cat_id, 'category');
      //   if ($category) {
      //     $category_slugs[] = $category->slug;
      //   }
      // }

      // if (!empty($category_slugs)) {
      //   $has_personalized_product = get_personalized_post($category_slugs, 'product', $num_items, 'slug');
      // }
    }
  }

  if ($has_personalized_product) {
    if ( WC() && WC()->session ) {
      WC()->session->set('user_algolia_products', $has_personalized_product);
    }
    return $has_personalized_product;
  }

  if ($always_show) {
    // Get all parent product categories
    $parent_categories = get_terms([
      'taxonomy' => 'product_cat',
      'hide_empty' => true,
      'parent' => 0
    ]);

    if (!empty($parent_categories)) {
      $parent_category_ids = wp_list_pluck($parent_categories, 'term_id');
      // Query for products in parent categories
      return get_personalized_post($parent_category_ids, 'product', $num_items);
    }
  }

  return [];
}

function get_featured_posts($num_items) {
  $has_personalized_post = false;
  // Check if the user is logged in
  if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    // Get user meta for selected_categories
    $selected_categories = get_user_meta($user_id, 'selected_categories', true);

    if (!empty($selected_categories)) {
      $category_names = get_post_category_names_from_ids($selected_categories);

      /**
       * Comment if you do not want post selections from Algolia index
       */
      // $has_personalized_post = algolia_search_posts_by_categories($category_names, $num_items);

      /**
       * Uncomment if you want post selections from WordPress database
       */
      // // Query for posts in selected categories
      // $category_slugs = array();
      // foreach ($selected_categories as $cat_id) {
      //   $category = get_term_by('id', $cat_id, 'category');
      //   if ($category) {
      //     $category_slugs[] = $category->slug;
      //   }
      // }

      // if (!empty($category_slugs)) {
      //   $has_personalized_post = get_personalized_post($category_slugs, 'post', $num_items, 'slug');
      // }
    }
  }

  if ($has_personalized_post) {
    if ( WC() && WC()->session ) {
      WC()->session->set('user_algolia_posts', $has_personalized_post);
    }
    return $has_personalized_post;
  }

  return [];
}

function get_personalized_post($category_terms, $post_type, $num_items, $field = 'id') {
  // Set the taxonomy based on the post type
  $taxonomy = ($post_type == 'product') ? 'product_cat' : 'category';

  $args = [
    'post_type' => $post_type,
    'posts_per_page' => -1,
    'tax_query' => [
      [
        'taxonomy' => $taxonomy,
        'field' => $field,
        'terms' => $category_terms,
        'include_children' => true
      ]
    ]
  ];

  $query = new WP_Query($args);
  $valid_posts = [];

  while ($query->have_posts()) {
    $query->the_post();

    if ($post_type == 'product') {
      $product = wc_get_product(get_the_ID());

      if ($product->get_stock_status() !== 'discontinued') {
        array_push($valid_posts, $product->get_id());
      }
    } else {
      array_push($valid_posts, get_the_ID());
    }
  }

  wp_reset_postdata();

  if (!empty($valid_posts)) {
    // Randomly pick the specified number of post/product IDs
    shuffle($valid_posts);
    $selected_posts = array_slice($valid_posts, 0, $num_items);

    $results = [];
    foreach ($selected_posts as $post_id) {
      $results[] = ($post_type == 'product') ? wc_get_product($post_id) : get_post($post_id);
    }
    return $results;
  }

  return [];
}
