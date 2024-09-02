<?php
/**
 * Register menus.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

function mayo_menu() {
	$locations = array(
		'header_menu'  => __('Header menu', 'mayo'),
		'account_menu' => __('Account menu', 'mayo'),
	);
	register_nav_menus($locations);
}

add_action('init', 'mayo_menu');
