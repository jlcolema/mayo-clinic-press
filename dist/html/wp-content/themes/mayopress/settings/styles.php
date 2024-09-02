<?php
/**
 * Register and enqueue styles.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */
function mayo_styles() {
  wp_register_style(
		'mayo-main',
		esc_url(MAYO_THEME_STYLES . 'main.css'),
		array(),
		MAYO_THEME_VERSION
	);

	// Add styles inline
	wp_add_inline_style('mayo-main', mayo_get_font_face_styles());

	// Enqueue theme stylesheet
  wp_enqueue_style('mayo-main');

	// Removes the default block CSS
	// wp_deregister_style('wp-block-library');

  // Load specific CSS for blocks
  if (has_block('core/media-text', get_the_ID())) {
		wp_enqueue_style('mayo-media-text', esc_url(MAYO_THEME_STYLES . 'block-core-media-text.css'));
	}

  if ( is_home() || is_front_page() ) {
		wp_enqueue_style('all-topics-css', esc_url(MAYO_THEME_STYLES . 'front_page.css'));
  }

  if ( is_page( 'videos' ) || is_page( 'podcasts' ) || is_page( 'topics' ) || is_category() ) {
		wp_enqueue_style('archives-css', esc_url(MAYO_THEME_STYLES . 'page_archives.css'));
  }

  if ( is_page( 'all-topics' ) ) {
		wp_enqueue_style('all-topics-css', esc_url(MAYO_THEME_STYLES . 'page_all_topics.css'));
  }

  if ( is_page_template('ad-hub-template.php') ) {
    wp_enqueue_style('ad-hub-css', esc_url(MAYO_THEME_STYLES . 'ad_hub.css'));
  }

  if ( is_page_template('rise-hub.php') ) {
    wp_enqueue_style('rise-hub-css', esc_url(MAYO_THEME_STYLES . 'rise_hub.css'));
  }

  if ( is_page_template('page-hub.php') ) {
		wp_enqueue_style('archives-css', esc_url(MAYO_THEME_STYLES . 'page_archives.css'));
    wp_enqueue_style('page-hub-css', esc_url(MAYO_THEME_STYLES . 'page_hub.css'));
  
    if ( is_page('parenting') ) {
      wp_enqueue_style('kids-hub-css', esc_url(MAYO_THEME_STYLES . 'kids_hub.css'));
    }
  }

  if ( is_search() ) {
		wp_enqueue_style('shop-css', esc_url(MAYO_THEME_STYLES . 'shop.css'));
		wp_enqueue_style('search-css', esc_url(MAYO_THEME_STYLES . 'search.css'));
  }

  if ( is_404() ) {
		wp_enqueue_style('404-css', esc_url(MAYO_THEME_STYLES . '404.css'));
  }

  if ( is_shop() || is_product_category() || is_tax( 'badge-tags' ) ) {
		wp_enqueue_style('shop-css', esc_url(MAYO_THEME_STYLES . 'shop.css'));
  }

  if ( is_singular( 'post' ) ) {
    wp_enqueue_style('ad-hub-css', esc_url(MAYO_THEME_STYLES . 'ad_hub.css'));
		wp_enqueue_style('mayo-single-post', esc_url(MAYO_THEME_STYLES . 'single.css'));
  }

  if ( is_singular( 'product' ) ) {
    wp_enqueue_style('mayo-single-product', esc_url(MAYO_THEME_STYLES . 'single_product.css'));
  }

  if ( is_singular( 'authors' ) ) {
    wp_enqueue_style('mayo-single-author', esc_url(MAYO_THEME_STYLES . 'single_authors.css'));
  }

  if ( is_archive( 'authors' ) ) {
    wp_enqueue_style('mayo-archive-author', esc_url(MAYO_THEME_STYLES . 'archive_authors.css'));
  }

  if ( is_page() ) {
		wp_enqueue_style('mayo-page', esc_url(MAYO_THEME_STYLES . 'page.css'));
  }

  if ( is_page_template('non-indexed-social-landing-page.php') ) {
    wp_enqueue_style('non-indexed-social-landing-page', esc_url(MAYO_THEME_STYLES . 'non_indexed_social_landing_page.css'));
  }

  if ( is_page( 'contact-us' ) ) {
		wp_enqueue_style('mayo-contact-page', esc_url(MAYO_THEME_STYLES . 'contact.css'));
  }

  if ( is_page( 'about-us' ) ) {
		wp_enqueue_style('mayo-about-page', esc_url(MAYO_THEME_STYLES . 'about.css'));
  }

  if ( is_page( 'our-impact' ) ) {
		wp_enqueue_style('mayo-impact-page', esc_url(MAYO_THEME_STYLES . 'impact.css'));
  }

  if ( is_account_page() || is_page( 'edit-my-list' ) || is_page( 'registration' ) ) {
    wp_enqueue_style('account-css', esc_url(MAYO_THEME_STYLES . 'account.css'));
  }

  if ( is_cart() ) {
		wp_enqueue_style('cart-css', esc_url(MAYO_THEME_STYLES . 'cart.css'));
  }

  if ( is_checkout() ) {
		wp_enqueue_style('checkout-css', esc_url(MAYO_THEME_STYLES . 'checkout.css'));
  }
}

add_action('wp_enqueue_scripts', 'mayo_styles');

/**
 * Enqueue editor styles.
 *
 * @since 1.0
 *
 * @return void
 */
function mayo_block_editor_styles() {
  wp_enqueue_style('mayo-editor-styles', MAYO_THEME_STYLES . 'editor.css', false);
}

add_action('enqueue_block_editor_assets', 'mayo_block_editor_styles');

/**
 * Inline fonts in admin area
 */
add_action('admin_head', function() {
  echo '<style>';
  echo mayo_get_font_face_styles();
  echo '</style>';
});


/**
 * Get font face styles.
 * Called by functions mayo_styles() and mayo_editor_styles() above.
 *
 * @since 1.0
 *
 * @return string
 */
function mayo_get_font_face_styles() {
	return "
  @font-face {
    font-family: 'Mayo Clinic Sans';
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url('" . esc_url(MAYO_THEME_FONTS . 'MayoClinicSans-Regular.woff2') . "') format('woff2');
  }

  @font-face {
    font-family: 'Mayo Clinic Sans';
    font-style: italic;
    font-weight: 400;
    font-display: swap;
    src: url('" . esc_url(MAYO_THEME_FONTS . 'MayoClinicSans-RegularItalic.woff2') . "') format('woff2');
  }

  @font-face {
    font-family: 'Mayo Clinic Sans';
    font-style: normal;
    font-weight: 700;
    font-display: swap;
    src: url('" . esc_url(MAYO_THEME_FONTS . 'MayoClinicSans-Bold.woff2') . "') format('woff2');
  }

  @font-face {
    font-family: 'Mayo Clinic Serif';
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url('" . esc_url(MAYO_THEME_FONTS . 'MayoClinicSerif-Regular.woff2') . "') format('woff2');
  }

  @font-face {
    font-family: 'Mayo Clinic Serif';
    font-style: normal;
    font-weight: 700;
    font-display: swap;
    src: url('" . esc_url(MAYO_THEME_FONTS . 'MayoClinicSerif-Bold.woff2') . "') format('woff2');
  }

  @font-face {
    font-family: 'Mayo Clinic Serif Display';
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url('" . esc_url(MAYO_THEME_FONTS . 'MayoClinicSerifDisplay-Regular.woff2') . "') format('woff2');
  }

  @font-face {
    font-family: 'Mayo Clinic Serif Display';
    font-style: normal;
    font-weight: 700;
    font-display: swap;
    src: url('" . esc_url(MAYO_THEME_FONTS . 'MayoClinicSerifDisplay-Bold.woff2') . "') format('woff2');
  }
	";
}

/**
 * Preloads the main web font to improve performance.
 *
 * Only the main web fonts (font-style: normal) are preloaded here since they are always relevant, while other
 * styles and weights may not be.
 *
 * @since 1.0
 *
 * @return void
 */
function mayo_preload_webfonts() {
	?>
	<link rel="preload" href="<?php echo esc_url(MAYO_THEME_FONTS . 'MayoClinicSans-Regular.woff2'); ?>" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="<?php echo esc_url(MAYO_THEME_FONTS . 'MayoClinicSerifDisplay-Regular.woff2'); ?>" as="font" type="font/woff2" crossorigin>
	<?php
}

add_action('wp_head', 'mayo_preload_webfonts');
