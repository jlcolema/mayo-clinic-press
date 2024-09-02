<?php

/**
 * Register block
 */
add_action('acf/init', function () {
  if (function_exists('acf_register_block_type')) {
    // Post carousel
    acf_register_block_type(array(
      'name'              => 'post-carousel',
      'title'             => __('Post Carousel', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/post-carousel.php',
      'keywords'          => array('carousel', 'slider', 'posts', 'products'),
      'category'          => 'design',
      'icon'              => 'editor-insertmore',
      'mode'              => 'preview',
      'supports'          => array( 'anchor' => true ),
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-post-carousel', MAYO_THEME_STYLES . 'block-post-carousel.css');
        wp_enqueue_script('block-post-carousel', MAYO_THEME_SCRIPTS . 'block-post-carousel.js', array(), null, true);
      }
    ));

    // Post carousel - manual selection
    acf_register_block_type(array(
      'name'              => 'post-carousel-manual',
      'title'             => __('Post Carousel - Manual Selection', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/post-carousel-manual.php',
      'keywords'          => array('carousel', 'slider', 'posts', 'products'),
      'category'          => 'design',
      'icon'              => 'editor-insertmore',
      'mode'              => 'preview',
      'supports'          => array( 'anchor' => true ),
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-post-carousel', MAYO_THEME_STYLES . 'block-post-carousel.css');
        wp_enqueue_script('block-post-carousel', MAYO_THEME_SCRIPTS . 'block-post-carousel.js', array(), null, true);
      }
    ));

    // Post carousel alt - manual selection
    acf_register_block_type(array(
      'name'              => 'post-carousel-alt-manual',
      'title'             => __('Post Carousel Alt - Manual Selection', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/post-carousel-alt-manual.php',
      'keywords'          => array('carousel', 'slider', 'posts', 'products'),
      'category'          => 'design',
      'icon'              => 'editor-insertmore',
      'mode'              => 'preview',
      'supports'          => array( 'anchor' => true ),
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-post-carousel', MAYO_THEME_STYLES . 'block-post-carousel.css');
        wp_enqueue_style('block-post-carousel-alt', MAYO_THEME_STYLES . 'block-post-carousel-alt.css');
        wp_enqueue_script('block-post-carousel', MAYO_THEME_SCRIPTS . 'block-post-carousel.js', array(), null, true);
      }
    ));

    // Caetegory carousel alt - manual selection
    acf_register_block_type(array(
      'name'              => 'category-carousel-alt-manual',
      'title'             => __('Category Carousel Alt - Manual Selection', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/category-carousel-alt-manual.php',
      'keywords'          => array('carousel', 'slider', 'posts', 'products'),
      'category'          => 'design',
      'icon'              => 'editor-insertmore',
      'mode'              => 'preview',
      'supports'          => array( 'anchor' => true ),
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-post-carousel', MAYO_THEME_STYLES . 'block-post-carousel.css');
        wp_enqueue_style('block-post-carousel-alt', MAYO_THEME_STYLES . 'block-post-carousel-alt.css');
        wp_enqueue_style('block-category-carousel-alt', MAYO_THEME_STYLES . 'block-category-carousel-alt.css');
        wp_enqueue_script('block-post-carousel', MAYO_THEME_SCRIPTS . 'block-post-carousel.js', array(), null, true);
      }
    ));

    // Featured posts
    acf_register_block_type(array(
      'name'              => 'featured-posts',
      'title'             => __('Featured Posts', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/featured-posts.php',
      'keywords'          => array('features', 'posts', 'hero'),
      'category'          => 'design',
      'icon'              => 'screenoptions',
      'mode'              => 'preview',
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-post-carousel', MAYO_THEME_STYLES . 'block-post-carousel.css');
        wp_enqueue_script('block-post-carousel', MAYO_THEME_SCRIPTS . 'block-post-carousel.js', array(), null, true);
      }
    ));

    // Icon Box
    acf_register_block_type(array(
      'name'              => 'icon-box',
      'title'             => __('Icon Box', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/icon-box.php',
      'keywords'          => array('icon', 'box'),
      'category'          => 'design',
      'icon'              => 'screenoptions',
      'mode'              => 'preview',
      'supports'          => array(
        'jsx' => true
      ),
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-icon-box', MAYO_THEME_STYLES . 'block-icon-box.css');
      }
    ));

    // Post accordion
    acf_register_block_type(array(
      'name'              => 'post-accordion',
      'title'             => __('Post Accordion', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/post-accordion.php',
      'keywords'          => array('accordion', 'faq', 'menu'),
      'category'          => 'design',
      'icon'              => 'editor-insertmore',
      'mode'              => 'auto',
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-post-carousel', MAYO_THEME_STYLES . 'block-post-accordion.css');
      }
    ));

    // Hub Hero
    acf_register_block_type(array(
      'name'              => 'hub-hero',
      'title'             => __('Hub Hero', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/hub-hero.php',
      'keywords'          => array('hub', 'banner', 'hero'),
      'category'          => 'common',
      'icon'              => 'superhero-alt',
      'mode'              => 'auto',
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-hub-hero', MAYO_THEME_STYLES . 'block-hub-hero.css');
      }
    ));

    // Kids Hub Hero
    acf_register_block_type(array(
      'name'              => 'kids-hub-hero',
      'title'             => __('Kids Hub Hero', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/kids-hub-hero.php',
      'keywords'          => array('kids', 'hub', 'banner', 'hero'),
      'category'          => 'common',
      'icon'              => 'superhero-alt',
      'mode'              => 'auto',
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-hub-hero', MAYO_THEME_STYLES . 'block-hub-hero.css');
        wp_enqueue_style('block-kids-hub-hero', MAYO_THEME_STYLES . 'block-kids-hub-hero.css');
      }
    ));

    // Podcast Series
    acf_register_block_type(array(
      'name'              => 'podcast-series',
      'title'             => __('Podcast Series', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/podcast-series.php',
      'keywords'          => array('podcast', 'series'),
      'category'          => 'common',
      'icon'              => 'playlist-audio',
      'mode'              => 'auto',
      'enqueue_assets'    => function() {
        wp_enqueue_style('block-podcast-series', MAYO_THEME_STYLES . 'block-podcast-series.css');
        wp_enqueue_script('block-podcast-series', MAYO_THEME_SCRIPTS . 'block-podcast-series.js', array(), null, true);
      }
    ));

    // CMS Conent
    acf_register_block_type(array(
      'name'              => 'cms-content',
      'title'             => __('CMS Content', 'mayo'),
      'render_template'   =>  MAYO_THEME_PATH . '/template-parts/blocks/cms-content.php',
      'keywords'          => array('CMS', 'API'),
      'category'          => 'common',
      'icon'              => 'search',
      'mode'              => 'auto'
    ));
  }
});

/**
 * Add fields
 */
require_once('fields/icon-picker/acf-icon-picker.php');
require_once('fields/image-size-select/acf-image-size-select.php');

/**
 * Add to customizer
 */
require_once('customizer.php');

/**
 * Make use of Timber for templating ACF blocks
 */
require_once('acf-timber.php');
