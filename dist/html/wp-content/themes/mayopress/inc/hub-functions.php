<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// related hub custom fields to only show pages with hub templates
add_filter('acf/fields/relationship/query/name=related_hub', function ($args, $field, $post_id) {
  $args['meta_key'] = '_wp_page_template';
  $args['meta_value'] = array( 'ad-hub-template.php', 'rise-hub.php', 'page-hub.php' );

  return $args;
}, 10, 3);
