<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin does not allow for saving of past available dates. Anonoymous instance of class
 * prevents the ability to remove the action in child theme. Modifying the plugin file
 * directly. class-wc-pre-orders-admin-products.php
 * Comment out where $timestamp is changed to blank if it is <= time().
 * In html-product-tab-options.php change options and set default to upfront
 * Will need to take care everytime we update the plugin until a new solution can be found.
 */
function update_product_meta($post_id, $post, $update) {
  if ('product' !== $post->post_type) {
    return;
  }

  $product = wc_get_product($post_id);
  $available_date = get_post_meta($post_id, '_wc_pre_orders_availability_datetime', true);

  if (!$available_date) {
    return;
  }
  // set preorder checkbox
  if ($available_date > time()) {
    update_post_meta($post_id, '_wc_pre_orders_enabled', 'yes');
  } else {
    update_post_meta($post_id, '_wc_pre_orders_enabled', 'no');
  }

  $time_difference = $available_date - time();
  $days_difference = round($time_difference / (60 * 60 * 24));

  // set badge tags
  if ($days_difference > 60) {
    // set badge tags to coming soon if greater than 60 days
    wp_set_object_terms($post_id, 'coming-soon', 'badge-tags', false);
  } elseif ($days_difference <= 60 && $days_difference > 0) {
    // remove badge tags if preorder less than 60 days
    wp_remove_object_terms($post_id, array('coming-soon', 'new-release'), 'badge-tags');
  } else if ($available_date <= time() && $days_difference <= 0 && $days_difference > -90) {
    // set badge tags to new release if within 90 days after release
    wp_set_object_terms($post_id, 'new-release', 'badge-tags', false);
  } else {
    // rmeove badge tags new release
    wp_remove_object_terms($post_id, array('coming-soon', 'new-release'), 'badge-tags');
  }
}
add_action('save_post', 'update_product_meta', 10, 3);

function check_product_availability_dates() {
  // Get only those products where _wc_pre_orders_enabled is set to 'yes'.
  $products = wc_get_products(array(
    'status' => 'publish',
    'limit' => -1,
    'meta_query' => array(
      array(
        'key' => '_wc_pre_orders_enabled',
        'value' => 'yes',
      )
    )
  ));

  // Loop through each product.
  foreach ($products as $product) {
    $product_id = $product->get_id();

    // Update the preorder checkbox and badge tags based on availability date.
    update_product_meta($product_id, get_post($product_id), false);
  }

  // Return a response. This is important for REST API responses.
  return new WP_REST_Response('Products checked successfully.', 200);
}

// add endpoint for cron job to point to
add_action('rest_api_init', function() {
  register_rest_route('schedule/v1', '/checkproductdates-mcpcpcspons', array(
    'methods' => 'GET',
    'callback' => 'check_product_availability_dates',
    'permission_callback' => function(WP_REST_Request $request) {
      $key = 'BZ14ClnZA5YuCKg3JQsD';

      // Get the 'sk' param from the request.
      $provided_key = $request->get_param('sk');

      // If the provided key matches the expected key, grant permission. Otherwise, deny it.
      return $provided_key === $key;
    }
  ));
});