<?php
/**
 * Load all theme setup files.
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

define('MAYO_THEME_VERSION', '1.0');
define('MAYO_THEME_PATH', get_template_directory());

/**
 * Get dev server config file if the dev server
 * is currently running and update URLs.
 */
$asset_url = get_template_directory_uri();
$devserver_file = false;

if (file_exists(MAYO_THEME_PATH . '/_build/devserver-live.json')) {
  $devserver_file = file_get_contents(MAYO_THEME_PATH . '/_build/devserver-live.json');
}

if ($devserver_file) {
  $devserver_json = json_decode($devserver_file, true);
  $url = parse_url($asset_url);
  $url_path = $url['path'];

  // remove WordPress subdirectory if applicable
  $url_array = explode('/', $url_path);
  while ($url_array[0] !== 'wp-content') {
    array_shift($url_array);
    $url_array = $url_array;
  }
  $url_path = implode('/', $url_array);

  $asset_url = $url['scheme'] . '://'
    . $devserver_json['host'] . ':'
    . $devserver_json['port'] . '/'
    . $url_path;
}

define('MAYO_THEME_URI', $asset_url);
define('MAYO_THEME_IMAGES', MAYO_THEME_URI . '/assets/img/');
define('MAYO_THEME_STYLES', MAYO_THEME_URI . '/assets/css/');
define('MAYO_THEME_SCRIPTS', MAYO_THEME_URI . '/assets/js/');
define('MAYO_THEME_FONTS', MAYO_THEME_URI . '/assets/fonts/');
define('MAYO_THEME_ICONS', MAYO_THEME_PATH . '/assets/icons/');

require_once 'settings/setup.php'; // Basic theme setup
require_once 'settings/styles.php'; // Registering CSS
require_once 'settings/scripts.php'; // Registering JS
require_once 'settings/menus.php'; // Registering menus
require_once 'settings/sidebars.php'; // Registering sidebars
require_once 'settings/optimization.php'; // Speeding up site
require_once 'settings/util.php'; // Other utility functions
require_once 'inc/block-patterns.php'; // Add block patterns
require_once 'inc/walker/init.php'; // Add custom nav walker to back end
require_once 'inc/acf/init.php'; // General ACF functions

/**
 * Timber functions.
 */
require get_template_directory() . '/inc/timber-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Personalized Experience functions.
 */
require get_template_directory() . '/inc/personalized-experience.php';

/**
 * Shop functions.
 */
require get_template_directory() . '/inc/shop-functions.php';

/**
 * Download functions.
 */
require get_template_directory() . '/inc/download-functions.php';

/**
 * Search functions.
 */
require get_template_directory() . '/inc/search-functions.php';

/**
 * FacetWP/SearchWP functions.
 */
require get_template_directory() . '/inc/facetwp-searchwp-functions.php';

/**
 * Cart/Checkout functions.
 */
require get_template_directory() . '/inc/cart-checkout-functions.php';

/**
 * Account functions.
 */
require get_template_directory() . '/inc/account-functions.php';

/**
 * Form functions.
 */
require get_template_directory() . '/inc/form-functions.php';

/**
 * Ensighten Tracking functions.
 */
require get_template_directory() . '/inc/ensighten_tracking.php';

/**
 * Custom SFG functions.
 */
require get_template_directory() . '/inc/sfg-functions.php';

/**
 * SEO functions.
 */
require get_template_directory() . '/inc/seo-functions.php';

/**
 * Hub functions.
 */
require get_template_directory() . '/inc/hub-functions.php';

/**
 * Product functions.
 */
require get_template_directory() . '/inc/product-functions.php';

/**
 * Privacy Policy functions.
 */
require get_template_directory() . '/inc/privacy-policy-functions.php';

// only display posts and products in search
function set_search_post_type($query)
{
  if ($query->is_search && !is_admin()) {
    $query->set('post_type', array('post', 'product'));
  }
  return $query;
}
add_filter('pre_get_posts', 'set_search_post_type');

// get featured slider post ids for exclusion from dynamic carousels
function get_featured_slider_post_ids($post_id)
{
  $featured_slider_post_ids = [];

  $blocks = parse_blocks(get_post_field('post_content', $post_id));
  foreach ($blocks as $block) {
    if ($block['blockName'] === 'acf/featured-slider') {
      foreach ($block['attrs']['data'] as $key => $value) {
        if (preg_match('/^featured_slides_\d+_featured_slide_post$/', $key)) {
          $featured_slider_post_ids = array_merge($featured_slider_post_ids, $value);
        }
      }
      break;
    }
  }

  return $featured_slider_post_ids;
}

// Change the `aria-label` attribute of the "Email Capture" form
add_filter('do_shortcode_tag', function($output, $tag, $attr) {
  if ('contact-form-7' !== $tag) {
    return $output;
  }

  if (!empty($attr['id'])) {
    $contact_form_id = (int) $attr['id'];

    // Check the form id and modify the output as required
    // Replace 61169 with your form's actual id
    if ($contact_form_id === 61169) {
      $output = str_replace('aria-label="Contact form"', 'aria-label="Email Subscription Form"', $output);
    }
  }

  return $output;
}, 10, 3);

add_action('wp_ajax_get_cart_count', 'my_custom_ajax_get_cart_count');
add_action('wp_ajax_nopriv_get_cart_count', 'my_custom_ajax_get_cart_count');
function my_custom_ajax_get_cart_count() {
  if ( WC() && WC()->cart ) {
    echo WC()->cart->get_cart_contents_count();
  } else {
    echo '0';
  }
  wp_die(); // this is required to terminate immediately and return a proper response
}

function format_runtime($runtime) {
  $formatted_time = '';
  if ($runtime['hours']) {
    $formatted_time .= $runtime['hours'] . ':';
  }
  if ($runtime['minutes']) {
    if (substr($formatted_time, -1) == ':' && $runtime['minutes'] < 10) {
      $formatted_time .= '0';
    }
    if ($runtime['minutes'] == 0) {
      $formatted_time .= '0:';
    } else {
      $formatted_time .= $runtime['minutes'] . ':';
    }
  } elseif (substr($formatted_time, -1) == ':') {
    $formatted_time .= '00:';
  }
  if ($runtime['seconds']) {
    if (substr($formatted_time, -1) == ':') {
      if ($runtime['seconds'] < 10) {
        $formatted_time .= '0';
      }
      if ($runtime['seconds'] == 0) {
        $formatted_time .= '0';
      } else {
        $formatted_time .= $runtime['seconds'];
      }
    } else {
      $formatted_time .= $runtime['seconds'] . ' seconds';
    }
  } elseif (substr($formatted_time, -1) == ':') {
    $formatted_time .= '00';
  }
  return $formatted_time;
}

// Remove `&nbsp;` that appears within the pricing area on Pre-Order and New Release PDPs
add_filter( 'woocommerce_get_price_html', 'custom_price_html', 100, 2 );
function custom_price_html( $price, $product ) {
  $price = str_replace('&nbsp;', '', $price);
  return $price;
}
