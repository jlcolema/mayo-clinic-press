<?php
/**
 * Template Name: Page Hub Template
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context              = Timber::context();
$context['page']      = new Timber\Post();
$context['hasAds']    = get_field('insert_ads');

// Set up post query
$args = array (
  'post_type'      => 'post',
  'cat'            => get_category_by_slug( get_post_field( 'post_name', get_post() ) )->term_id,
  'orderby'        => 'date',
  'order'          => 'DESC',
  'posts_per_page' => 12,
  'facetwp'        => true
);
$context['post_query'] = Timber::get_posts($args);

Timber::render('templates/page-hub.twig', $context);
