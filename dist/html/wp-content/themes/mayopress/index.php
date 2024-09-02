<?php
/**
 * Controller of the main template.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context          = Timber::context();
$context['posts'] = new Timber\PostQuery();

Timber::render('templates/index.twig', $context);
