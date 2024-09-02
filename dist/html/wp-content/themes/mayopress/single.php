<?php
/**
 * Controller of the post template.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context                        = Timber::context();
$context['post']                = new Timber\Post();
$context['category']            = get_the_category( $post->ID );
$context['has_parenting_cat']   = has_category( 'parenting' );
$context['pages']               = get_pages();

$post_terms = wp_get_post_terms( $post->ID, 'category' );
$tax_query = array('relation' => 'OR');

// Arrange taxonomy query
if ($post_terms) {
  foreach ($post_terms as $term) {
    $arr = array(
      'taxonomy' => $term->taxonomy,
      'field'    => 'term_id',
      'terms'    => $term->term_id
    );

    $tax_query[] = $arr;
  }
}

// Set up post query
$args = array (
  'post_type'      => 'post',
  'tax_query'      => $tax_query,
  'orderby'        => 'date',
  'order'          => 'DESC',
  'posts_per_page' => 8
);

$context['post_query'] = Timber::get_posts($args);

Timber::render('templates/single.twig', $context);
