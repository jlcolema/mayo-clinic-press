<?php
/**
 * Controller of the search results template.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context                 = Timber::context();
$context['posts']        = new Timber\PostQuery();
$context['search_query'] = get_search_query();
$context['sidebar'] = 'Search facets widget area';
$context['is_mobile'] = wp_is_mobile();

if ( get_query_var( 'view' ) && get_query_var ( 'view' ) === 'list' ) {
	$context['view'] = 'list';
} else {
	$context['view'] = 'grid';
}

if ( get_query_var ( '_products_articles' ) === 'post' ) {
	$context['post_type'] = 'post';
} else if ( get_query_var ( '_products_articles' ) === 'product' ) {
	$context['post_type'] = 'product';
}

Timber::render('templates/search.twig', $context);
