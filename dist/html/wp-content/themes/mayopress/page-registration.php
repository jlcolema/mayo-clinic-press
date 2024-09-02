<?php
/**
 * Controller of the page template.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context              = Timber::context();
$context['page']      = new Timber\Post();

Timber::render('templates/page-registration.twig', $context);
