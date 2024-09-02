<?php
/**
 * Controller of the category template.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context              = Timber::context();
$context['category']  = new Timber\Term();
$context['post_query'] = new Timber\PostQuery();

Timber::render('templates/category.twig', $context);
