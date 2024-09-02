<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$object_id   = $item->object_id;
$object_type = $item->object_type;
$cat_id      = $item->cat_id;
$user_id     = $item->user_id;

global $wpdb;
$cbxwpbookmak_category_table = $wpdb->prefix . 'cbxwpbookmarkcat';

$catPrivacyResult = $wpdb->get_results(
	$wpdb->prepare("SELECT privacy FROM $cbxwpbookmak_category_table WHERE user_id = %d AND id = %d", $user_id, $cat_id)
);

$catPrivacy = '';
if (!empty($catPrivacyResult)) {
	$catPrivacy = ' data-privacy="' . esc_attr($catPrivacyResult[0]->privacy) . '"';
}

echo '<li class="cbxwpbookmark-mylist-item ' . $sub_item_class . '"' . $catPrivacy . ' data-cat-id="' . $cat_id . '">';
do_action( 'cbxwpbookmark_bookmarkpost_single_item_start', $object_id, $item );
echo '<a href="' . get_permalink( $object_id ) . '">' . wp_strip_all_tags( get_the_title( $object_id ) ) . '</a>' . $action_html;
do_action( 'cbxwpbookmark_bookmarkpost_single_item_end', $object_id, $item );
echo '</li>';