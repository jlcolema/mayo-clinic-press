<?php
/**
 * Controller of the post template.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context              = Timber::context();
$context['post']      = new Timber\Post();



Timber::render('templates/single-authors.twig', $context);
