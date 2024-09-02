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
$context['hasAds']    = get_field('insert_ads');

Timber::render('templates/page.twig', $context);
