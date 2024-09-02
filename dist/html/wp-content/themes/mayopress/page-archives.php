<?php
/**
 * Template Name: Archives Page
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context                = Timber::context();
$context['page']        = new Timber\Post();
$context['pathname']    = Timber\URLHelper::get_params();
$context['query_cat']   = get_query_var( '_listing_categories' );
$context['use_recent']  = get_field( 'featured_use_recent' );

// Set up post query
$args = array (
  'post_type'      => 'post',
  'orderby'        => 'date',
  'order'          => 'DESC',
  'posts_per_page' => 12,
  'facetwp'        => true
);
$context['post_query'] = Timber::get_posts($args);

// Set up product query
$args = array (
  'post_type'      => 'product',
  'orderby'        => 'date',
  'order'          => 'DESC',
  'posts_per_page' => 8
);
$context['prod_query'] = Timber::get_posts($args);

if ( get_field( 'featured_use_recent' ) ) {
  $featured_format = get_field( 'post_format' );
  $tax_query = '';
  
  if ( $featured_format != 'all' ) {
    $tax_query = array(
      array(
        'taxonomy' => 'content-formats',
        'field' => 'slug',
        'terms' => $featured_format
      )
    );
  }

  // Set up featured query
  $args = array (
    'post_type'      => 'post',
    'tax_query'      => $tax_query,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'posts_per_page' => 1
  );
  $context['featured'] = Timber::get_posts($args);
}

Timber::render('templates/page-archives.twig', $context);
