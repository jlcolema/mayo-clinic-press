<?php
/**
 * Main configurations of the theme.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */
function mayo_setup() {
	// Sets the maximum allowed width for any content in the theme
	if (!isset($content_width)) {
		$content_width = 1280;
	}

	// Enable post thumbnails support
	add_theme_support('post-thumbnails');

	// Add additional image sizes
	add_image_size('excerpt-thumbnail', 526, 352, true);
	add_image_size('singular-thumbnail', 1120, 640, true);
	add_image_size('main-hero', 640, 514, true);
	add_image_size('card-thumbnail', 608, 348, true);
	add_image_size('featured-thumbnail', 850, 570, true);

  function add_image_size_names($sizes) {
    return array_merge($sizes, array(
      'main-hero' => __('Main Hero', 'mayo'),
      'card-thumbnail' => __('Card Thumbnail', 'mayo'),
      'featured-thumbnail' => __('Featured Thumbnail', 'mayo'),
    ));
  }

  add_filter('image_size_names_choose', 'add_image_size_names');

	// Add support for block styles
	add_theme_support('wp-block-styles');

	// Enables custom logo support
	add_theme_support('custom-logo');

	// Enables of use of HTML5 markup
	add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));

	// Allows plugins to manage the document title tag
	add_theme_support('title-tag');

  // Add WooCommerce support
  add_theme_support('woocommerce');

  // Modify excerpt
  add_filter('excerpt_length', function() {
    return 25;
  });

  add_filter('excerpt_more', function() {
    return '&hellip;';
  });

  add_theme_support('editor-color-palette', array(
    array(
      'slug'  => "black",
      'color' => "#000000",
      'name'  => "Black"
    ),
    array(
      'slug'  => "white",
      'color' => "#ffffff",
      'name'  => "White"
    ),
    array(
      'slug'  => "primary",
      'color' => "#0057b8",
      'name'  => "Primary"
    ),
    array(
      'slug'  => "primary-dark",
      'color' => "#004395",
      'name'  => "Primary (Dark)"
    ),
    array(
      'slug'  => "secondary",
      'color' => "#009cde",
      'name'  => "Secondary"
    ),
    array(
      'slug'  => "accent-1",
      'color' => "#9cdbd9",
      'name'  => "Accent 1"
    ),
    array(
      'slug'  => "accent-2",
      'color' => "#00873e",
      'name'  => "Accent 2"
    ),
    array(
      'slug'  => "accent-3",
      'color' => "#8246af",
      'name'  => "Accent 3"
    ),
    array(
      'slug'  => "accent-4",
      'color' => "#e4002b",
      'name'  => "Accent 4"
    ),
    array(
      'slug'  => "accent-5",
      'color' => "#fe5000",
      'name'  => "Accent 5"
    ),
    array(
      'slug'  => "accent-6",
      'color' => "#ffc845",
      'name'  => "Accent 6"
    ),
    array(
      'slug'  => "light-gray",
      'color' => "#d9d9d6",
      'name'  => "Light Gray"
    ),
    array(
      'slug'  => "medium-gray",
      'color' => "#c8c8c8",
      'name'  => "Medium Gray"
    ),
    array(
      'slug'  => "dark-gray",
      'color' => "#a8a8a8",
      'name'  => "Dark Gray"
    )
  ));
}

add_action('after_setup_theme', 'mayo_setup');
