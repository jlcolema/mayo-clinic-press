<?php
/**
 * Register and enqueue scripts.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

add_action('wp_enqueue_scripts', 'mayo_scripts');
function mayo_scripts() {
	wp_register_script(
    'mayo-main',
    esc_url(MAYO_THEME_SCRIPTS . 'main.js'),
    array(),
    MAYO_THEME_VERSION,
    true
  );

  // Enqueue theme stylesheet
	wp_enqueue_script('mayo-main');

  // Localize the script with the AJAX URL
  wp_localize_script('mayo-main', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}

add_action('admin_enqueue_scripts', 'mayo_admin_scripts');
function mayo_admin_scripts() {
	wp_register_script(
    'mayo-admin',
    esc_url(MAYO_THEME_SCRIPTS . 'admin.js'),
    array(),
    MAYO_THEME_VERSION,
    true
  );

  // Enqueue theme stylesheet
	wp_enqueue_script('mayo-admin');
}
