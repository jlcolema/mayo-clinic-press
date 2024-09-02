<?php
/**
 * Template Name: Non-Indexed Social Landing Page Template
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context              = Timber::context();
$context['page']      = new Timber\Post();

Timber::render('templates/non-indexed-social-landing-page.twig', $context);
