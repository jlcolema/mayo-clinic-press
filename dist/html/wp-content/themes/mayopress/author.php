<?php
/**
 * Controller of the author page template.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context                = Timber::context();
$context['author_name'] = get_the_author_meta('display_name');
$context['author_bio']  = get_the_author_meta('description');
$context['posts']       = new Timber\PostQuery();

Timber::render('templates/author.twig', $context);
