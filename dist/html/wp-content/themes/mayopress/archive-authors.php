<?php
/**
 * Controller of the archive, authors post-type template.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context = Timber::get_context();
$context['title'] = 'Authors & Experts';

// Set up post query
$args = array (
  'post_type'      => 'authors',
  'orderby'        => 'title',
  'order'          => 'ASC',
  'posts_per_page' => -1
);

$context['posts'] = Timber::get_posts( $args );

Timber::render('templates/archive-authors.twig', $context);
