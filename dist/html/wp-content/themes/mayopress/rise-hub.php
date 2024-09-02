<?php
/**
 * Template Name: RISE Hub Template
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context              = Timber::context();
$context['page']      = new Timber\Post();

Timber::render('templates/rise-hub-template.twig', $context);
