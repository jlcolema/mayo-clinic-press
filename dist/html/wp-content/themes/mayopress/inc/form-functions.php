<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// replaces values with 'pageurl' or 'pagetitle' with dynamic values
add_filter( 'wpcf7_form_elements', function($form) { 
  $url = esc_url( site_url() . $_SERVER['REQUEST_URI'] );
  if ( is_search() ) {
    $page_title = 'Search';
  } else {
    $page_title = esc_attr( get_the_title() );
  }

  $form = str_replace( 'pageurl', $url, $form );
  $form = str_replace( 'pagetitle', $page_title, $form );
  return $form;
} );

// Remove <p> and <br/> from Contact Form 7
add_filter('wpcf7_autop_or_not', '__return_false');
