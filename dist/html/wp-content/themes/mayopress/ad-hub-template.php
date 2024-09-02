<?php
/**
 * Template Name: Ad Hub Template
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */
$context              = Timber::context();
$context['post']      = new Timber\Post();


Timber::render('templates/ad-hub-template.twig', $context);
