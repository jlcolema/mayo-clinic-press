<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PLP/Search page functions
 */

// remove default ratings, result_count, sorting, and pagination
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title_cross', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_rating', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
// move default sale badge to woocommerce_shop_loop_item_title
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 5 );
// move add to cart button to woocommerce_after_shop_loop_item_title
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'conditionally_add_to_cart_button', 30 );

function conditionally_add_to_cart_button() {
  // do not display on badge-tags page, coming soon books
  if ( !is_tax( 'badge-tags' ) ) {
    woocommerce_template_loop_add_to_cart();
  }
}



// add facetwp result count pager at top and bottom of list
add_action( 'woocommerce_before_shop_loop', 'woocommerce_facetwp_result_count', 20 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_facetwp_result_count', 10 );
function woocommerce_facetwp_result_count() {
  echo '<div class="facetwp__results-count">Showing ' . facetwp_display('facet','results_count') . '</div>';
}

// add view toggle button
add_action( 'woocommerce_before_shop_loop', 'woocommerce_add_view_toggle', 30 );
function woocommerce_add_view_toggle() {
  echo '<div class="view__container is-hidden--mobile" data-component="view-toggle">';
  echo '<p class="view__label">VIEW:</p>';
  echo '<div class="options__container">';
  echo '<button type="button" data-view="grid" class="view__option view__option--grid">';
  echo '<svg class="icon icon--grid" aria-hidden="true" focusable="false"><use xlink:href="#grid"></use></svg>';
  echo ' Grid ';
  echo '</button>';
  echo '<button type="button" data-view="list" class="view__option view__option--list">';
  echo '<svg class="icon icon--list-2" aria-hidden="true" focusable="false"><use xlink:href="#list-2"></use></svg>';
  echo ' List ';
  echo '</button>';
  echo '</div>';
  echo '</div>';
}

// check new release term and remove if over 90 days
add_action( 'woocommerce_shop_loop_item_title', 'check_new_release', 4 );
function check_new_release() {
  global $post;

  $post_id = $post->ID;
  $available_date = get_post_meta($post_id, '_wc_pre_orders_availability_datetime', true);

  if ( $available_date ) {
    update_product_meta($post_id, $post, false);
  } else if ( has_term( 'new-release', 'badge-tags', $post ) && strtotime( get_the_date( '', $post->ID ) ) < strtotime( '-90 days' ) ) {
    wp_remove_object_terms( $post->ID, 'new-release', 'badge-tags' );
  }
}

// add author and format to product
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_meta', 20 );
function woocommerce_template_loop_product_meta() {
  global $product;

  // get authors
  $authors = get_field( 'product_authors' );
  if ( $authors ) {
    $format_authors = array();
    foreach ( $authors as $author ) {
      setup_postdata( $author );
      $format_authors[] = get_the_title( $author -> ID );
      wp_reset_postdata();
    }
    $author_markup = '<p class="product__author">by ' . join( ', ', $format_authors ) . '</p>';
    echo $author_markup;
  }
  
  // get formats
  $terms = get_the_terms( $product->get_id(), 'product-formats' );
  if ( $terms && ! is_wp_error( $terms ) ) {
    $format_names = array();
    foreach ( $terms as $term ) {
      $format_names[] = $term->name;
    }
    $format_markup = '<p class="product__format">' . join( ', ', $format_names ) . '</p>';
    echo $format_markup;
  }
}

// add plp description to product
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_plp_description', 40 );
function woocommerce_template_loop_product_plp_description() {
  global $post;

  $description = get_field( 'product_listing_description' );
  if ( empty( $description ) ) {
    if ( is_null( $product ) ) {
      $product = wc_get_product( $post->ID );
    }

    $full_description = $product->description;
    $description = wp_trim_words( $full_description, 50, '...' );
  }

  if ( ! empty( $description ) ) {
    echo '<div class="product__description">' . $description . '</div>';
  }
}

// add percentage saved
add_filter( 'woocommerce_get_price_html', 'woocommerce_percentage_on_sale' );
function woocommerce_percentage_on_sale($html){
  global $product;
  if( $product && $product->is_type('simple') ) {
    $regular_price     = get_post_meta( $product->get_id(), '_regular_price', true );
    $sale_price     = get_post_meta( $product->get_id(), '_sale_price', true );

    if( !empty($sale_price) ) {
      $amount_saved = $regular_price - $sale_price;
      $currency_symbol = get_woocommerce_currency_symbol();
      $percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );

      return $html . '<p class="onsale__percent">' . number_format($percentage,0, '', '') . '% OFF</p>';
    }
  }
  return $html;
}

// add view details link
add_action( 'woocommerce_after_shop_loop_item_title', 'view_details_link', 40 );
function view_details_link() {
  echo '<div class="item__view-details">';
  do_action( 'woocommerce_before_shop_loop_item' );
  echo 'View Details';
  do_action( 'woocommerce_after_shop_loop_item' );
  echo '</div>';
}

// add plp banner
add_action( 'plp_banner', 'display_plp_banner' );
function display_plp_banner() {
  $banner_text = get_option( 'woocommerce_plp_banner_text' );
  $banner_button = get_option( 'woocommerce_plp_banner_button' );
  $banner_link = get_option( 'woocommerce_plp_banner_url' );
  $banner_color = get_option( 'woocommerce_plp_banner_color_scheme' );
  echo '<li class="plp-banner__container color-' . $banner_color . '" style="--color: var(--color-' . $banner_color . ')">';
  echo '<p>' . $banner_text . '</p>';
  if ( '' !== $banner_link ) {
    echo '<a href="' . $banner_link . '">' . $banner_button . ' <svg class="icon icon--arrow-right" aria-hidden="true" focusable="false"><use xlink:href="#arrow-right"></use></svg></a>';
  }
  echo '</li>';
}

// add facetwp pagination
add_action( 'woocommerce_after_shop_loop', 'woocommerce_facetwp_pagination', 20 );
function woocommerce_facetwp_pagination() {
  echo facetwp_display('facet','pagination');
}

// change the Number of WooCommerce Products Displayed Per Page
add_filter( 'loop_shop_per_page', 'lw_loop_shop_per_page', 30 );
function lw_loop_shop_per_page( $products ) {
  if ( wp_is_mobile() ) {
    $products = 24;
  } else {
    $products = 48;
  }
  return $products;
}

// change see more facets text
add_filter( 'gettext', function ( $translated_text, $text, $domain ) {
  if ( 'fwp-front' == $domain && 'See {num} more' == $translated_text ) {
      $translated_text = '+ View All';
  }
  return $translated_text;
}, 10, 3 );

// change posts label to topics and yes (preorders) to pre-orders
add_filter( 'facetwp_facet_display_value', function( $label, $params ) {
  if ( 'Posts' == $label ) {
      $label = 'Topics';
  } else if ( 'WooCommerce Products' == $label ) {
    $label = 'Products';
  } else if ( 'yes' == $label && 'preorders' == $params['facet']['name']) {
    $label = 'Pre-Orders';
  }
  return $label;
}, 10, 2 );

/**
 * PDP functions
 */

// move default sale badge to woocommerce_shop_loop_item_title
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_sale_flash', 3 );

// check new release term and remove if over 90 days
add_action( 'woocommerce_single_product_summary', 'check_new_release', 2 );

// add author to product
add_action( 'woocommerce_single_product_summary', 'woocommerce_product_author', 7 );
function woocommerce_product_author() {
  $authors = get_field( 'product_authors' );

  if ( $authors ) {
    $author_markup = '<p class="product__author">by ';
    foreach ( $authors as $author ) {
      setup_postdata( $author );
      $author_markup = $author_markup  . get_the_title( $author -> ID ) . ', ';
      wp_reset_postdata();
    }
    $author_markup = substr($author_markup, 0 , -2) . '</p>';
    echo $author_markup;
  }
}

// add div around woocommerce_template_single_price
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price_with_container', 10 );
function woocommerce_template_single_price_with_container() {
  global $product;

  echo '<div class="price__container">';
    echo '<div class="price__single">';
      woocommerce_template_single_price();
    echo '</div>';
    // Check if the product is a pre-order
    // If it is, display the "released at a future date" message
    // If it is not, display the "ships within 1-2 business days" message
    if ( class_exists( 'WC_Pre_Orders_Product' ) && WC_Pre_Orders_Product::product_can_be_pre_ordered($product) ) {
      $wc_pre_order = new WC_Pre_Orders_Product();
      echo '<p>';
      $wc_pre_order->add_pre_order_product_message();
      echo '</p>';
    } else {
      echo '<p>Ships within 1-2 business days</p>';
    }
  echo '</div>';
}

// remove product meta
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// create action mobile_product_title
add_action( 'mobile_product_title', 'check_new_release', 2 );
add_action( 'mobile_product_title', 'woocommerce_show_product_sale_flash', 3);
add_action( 'mobile_product_title', 'woocommerce_template_single_title', 5);
add_action( 'mobile_product_title', 'woocommerce_product_author', 7);
add_action( 'mobile_product_title', 'woocommerce_template_single_price_with_container', 9);

// remove character in subscription prefix
add_filter( 'woocommerce_get_price_html_from_text', function( $string ) {
  $string = '<span class="from">' . _x( 'From', 'min_price', 'woocommerce' ) . ' </span>';
  return $string;
}, 10, 1 );

// change subscription variations to radio boxes
add_filter('woocommerce_dropdown_variation_attribute_options_html', 'variation_radio_buttons', 20, 2);
function variation_radio_buttons($html, $args) {
  $args = wp_parse_args(apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args), array(
    'options'          => false,
    'attribute'        => false,
    'product'          => false,
    'selected'         => false,
    'name'             => '',
    'id'               => '',
    'class'            => '',
    'show_option_none' => __('Choose an option', 'woocommerce'),
  ));

  if(false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product) {
    $selected_key     = 'attribute_'.sanitize_title($args['attribute']);
    $args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key])) : $args['product']->get_variation_default_attribute($args['attribute']);
  }

  $options               = $args['options'];
  $product               = $args['product'];
  $attribute             = $args['attribute'];
  $name                  = $args['name'] ? $args['name'] : 'attribute_'.sanitize_title($attribute);
  $id                    = $args['id'] ? $args['id'] : sanitize_title($attribute);
  $class                 = $args['class'];
  $show_option_none      = (bool)$args['show_option_none'];
  $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __('Choose an option', 'woocommerce');
  $show_option_period    = empty(WC_Subscriptions_Product::get_period( $product )) ? '' : ' / ' . WC_Subscriptions_Product::get_period( $product );

  if(empty($options) && !empty($product) && !empty($attribute)) {
    $attributes = $product->get_variation_attributes();
    $options    = $attributes[$attribute];
  }

  $radios = '<div class="variation-radios" data-component="variation-radios">';

  if(!empty($options)) {
    if($product && taxonomy_exists($attribute)) {
      $terms = wc_get_product_terms($product->get_id(), $attribute, array(
        'fields' => 'all',
      ));

      foreach($terms as $term) {
        if(in_array($term->slug, $options, true)) {
          $radios .= '<label for="'.sanitize_title_with_dashes( esc_attr($term->slug) ).'"><input type="radio" id="'.sanitize_title_with_dashes( esc_attr($term->slug) ).'" name="'.esc_attr($name).'" value="'.esc_attr($term->slug).'">'.esc_html(apply_filters('woocommerce_variation_option_name', $term->name)).$show_option_period.'</label>';
        }
      }
    } else {
      foreach($options as $option) {
        $radios    .= '<label for="'.sanitize_title_with_dashes( esc_attr($option) ).'"><input type="radio" id="'.sanitize_title_with_dashes( esc_attr($option) ).'" name="'.esc_attr($name).'" value="'.esc_attr($option).'" id="'.sanitize_title($option).'">'.esc_html(apply_filters('woocommerce_variation_option_name', $option)).$show_option_period.'</label>';
      }
    }
  }
  $radios .= '</div>';
  return $html.$radios;
}

function variation_check($active, $variation) {
  if(!$variation->is_in_stock() && !$variation->backorders_allowed()) {
    return false;
  }
  return $active;
}
add_filter('woocommerce_variation_is_active', 'variation_check', 10, 2);

add_filter( 'woocommerce_variation_option_name', 'display_price_in_variation_option_name' );
function display_price_in_variation_option_name( $term ) {
  global $wpdb, $product;

  if ( empty( $term ) ) return $term;
  if ( empty( $product->id ) ) return $term;

  $id = $product->get_id();

  $result = $wpdb->get_col( "SELECT slug FROM {$wpdb->prefix}terms WHERE name = '$term'" );

  $term_slug = ( !empty( $result ) ) ? $result[0] : $term;

  $query = "SELECT postmeta.post_id AS product_id
              FROM {$wpdb->prefix}postmeta AS postmeta
                  LEFT JOIN {$wpdb->prefix}posts AS products ON ( products.ID = postmeta.post_id )
              WHERE postmeta.meta_key LIKE 'attribute_%'
                  AND postmeta.meta_value = '$term_slug'
                  AND products.post_parent = $id";

  $variation_id = $wpdb->get_col( $query );

  $parent = wp_get_post_parent_id( $variation_id[count( $variation_id ) - 1] );

  if ( $parent > 0 ) {
    $_product = new WC_Product_Variation( $variation_id[count( $variation_id ) - 1] );
    return $term . '---' . wp_kses( woocommerce_price( $_product->get_price() ), array() );
  }
  return $term;
}

/**
 * Callback function that returns true if the current page is a WooCommerce page or false if otherwise.
 *
 * @return boolean true for WC pages and false for non WC pages
 */
function is_wc_page() {
	return is_search() || is_woocommerce() || is_cart() || is_checkout() || is_account_page();
}

/**
 * Remove WC stuff on non WC pages.
 */
add_action( 'template_redirect', 'conditionally_remove_wc_assets' );
function conditionally_remove_wc_assets() {
	if ( is_wc_page() ) {
		return;
	}

	// remove WC generator tag
	remove_filter( 'get_the_generator_html', 'wc_generator_tag', 10, 2 );
	remove_filter( 'get_the_generator_xhtml', 'wc_generator_tag', 10, 2 );

	// unload WC scripts
	remove_action( 'wp_enqueue_scripts', [ WC_Frontend_Scripts::class, 'load_scripts' ] );
	remove_action( 'wp_print_scripts', [ WC_Frontend_Scripts::class, 'localize_printed_scripts' ], 5 );
	remove_action( 'wp_print_footer_scripts', [ WC_Frontend_Scripts::class, 'localize_printed_scripts' ], 5 );

	// remove "Show the gallery if JS is disabled"
	remove_action( 'wp_head', 'wc_gallery_noscript' );

	// remove WC body class
	remove_filter( 'body_class', 'wc_body_class' );
}

add_filter( 'woocommerce_enqueue_styles', 'conditionally_woocommerce_enqueue_styles' );

/**
 * Unload WC stylesheets on non WC pages
 *
 * @param array $enqueue_styles
 */
function conditionally_woocommerce_enqueue_styles( $enqueue_styles ) {
	return is_wc_page() ? $enqueue_styles : array();
}

add_action( 'wp_enqueue_scripts', 'conditionally_wp_enqueue_scripts' );
function conditionally_wp_enqueue_scripts() {
	if ( ! is_wc_page() ) {
		wp_dequeue_style( 'woocommerce-inline' );
	}
}

add_action( 'init', 'remove_wc_custom_action' );
function remove_wc_custom_action(){
	remove_action( 'wp_head', 'wc_gallery_noscript' );
}

/**
 * only add one variant of healthletter to the cart.
 */
add_action( 'woocommerce_add_to_cart', 'change_cart_variant', 100, 6 );
function change_cart_variant( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
  // get cart item data
  $cart_items = WC()->cart->get_cart();

  if ( $variation_id == 41815 || $variation_id == 47981 ) {
    // adding Print & Digital variant
    // loop through cart
    foreach ( $cart_items as $key => $cart_item ) {
      // if Print or Digital variant is found in the cart
      if ( $cart_item['variation_id'] == 41813
        || $cart_item['variation_id'] == 47982
        || $cart_item['variation_id'] == 41814 ) {
          // remove Print or Digital variant
          WC()->cart->remove_cart_item($key);
          break;
      }
    }
  } else if ( $variation_id == 41813 || $variation_id == 47982 ) {
    // adding Print variant
    // loop through cart
    foreach ( $cart_items as $key => $cart_item ) {
      // if Digital variant is found in the cart
      if ( $cart_item['variation_id'] == 41814 ) {
        // remove Digital variant
        WC()->cart->remove_cart_item($key);

        // add correct locale item
        if ( $variation_id == 41813 ) {
          $add_to_cart = 41815;
        } else {
          $add_to_cart = 47981;
        }

        // add Print & Digital variant
        WC()->cart->add_to_cart(41812, $quantity, $add_to_cart);
        break;
      } // if Print & Digital variant is found in the cart
      else if ( $cart_item['variation_id'] == 41815 || $cart_item['variation_id'] == 47981 ) {
        // remove this Print variant
        WC()->cart->remove_cart_item($cart_item_key);
        // add notice that they already have Print & Digital in their cart
        wc_add_notice( __( 'You already have the Print & Digital version in your cart' ), 'notice' );
        break;
      }
    }
  } else if ( $variation_id == 41814 ) {
    // adding Digital variant
    // loop through cart
    foreach ( $cart_items as $key => $cart_item ) {
      // if Print variant is found in the cart
      if ( $cart_item['variation_id'] == 41813 || $cart_item['variation_id'] == 47982 ) {
        // remove Print variant
        WC()->cart->remove_cart_item($key);

        // add correct locale item
        if ( $cart_item['variation_id'] == 41813 ) {
          $add_to_cart = 41815;
        } else {
          $add_to_cart = 47981;
        }

        // add Print & Digital variant
        WC()->cart->add_to_cart(41812, $quantity, $add_to_cart);
        break;
      } // if Print & Digital variant is found in the cart
      else if ( $cart_item['variation_id'] == 41815 || $cart_item['variation_id'] == 47981 ) {
        // remove this Digital variant
        WC()->cart->remove_cart_item($cart_item_key);
        // add notice that they already have Print & Digital in their cart
        wc_add_notice( __( 'You already have the Print & Digital version in your cart' ), 'notice' );
        break;
      }
    }
  }
}

/**
 * If pre-order is more than 90 days, display notification form instead
 */
function is_preorder_available( $product_id ) {
  $pre_order_threshold = get_field('preorder_threshold', 'external_settings');
  $pre_orders_enabled = get_post_meta( $product_id, '_wc_pre_orders_enabled', true );
  $pre_orders_availability_date = get_post_meta( $product_id, '_wc_pre_orders_availability_datetime', true );

  if ( $pre_orders_enabled === 'yes' && ! empty( $pre_orders_availability_date ) ) {
    $current_date = new DateTime();
    $availability_date = DateTime::createFromFormat('U', $pre_orders_availability_date);
    $interval = $current_date->diff( $availability_date );
    $days_difference = $interval->days;

    if ( $pre_order_threshold && $days_difference > $pre_order_threshold ) {
      return false;
    }
  }

  return true;
}

function display_preorder_form() {
  global $product;
  $product_title = $product->get_title();

  echo '<div id="preorder" class="preorder__container form-messaging" data-component="form-tracking" data-hide-selectors=".preorder__title, .preorder__container form.wpcf7-form &gt; :not(.wpcf7-response-output)">';
  echo '<p class="preorder__title"><strong>' . $product_title . ' is not yet available for pre-order. Sign up below to be notified when pre-orders open.</strong></p>';
  echo do_shortcode( '[contact-form-7 id="93092" title="Preorder Form"]' );
  echo do_shortcode('[wc_wishlists_button]');
  echo '</div>';
}

function replace_add_to_cart_with_preorder_form() {
  $preorder_form = get_field('preorder_form', 'external_settings');
  if ($preorder_form) {
    global $product;
    $product_id = $product->get_id();

    if ( !is_preorder_available( $product_id ) ) {
      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
      add_action( 'woocommerce_single_product_summary', 'display_preorder_form', 30 );
    }
  }
}
add_action( 'woocommerce_before_single_product', 'replace_add_to_cart_with_preorder_form' );

function exclude_coming_soon_from_shop($query) {
  if (!is_admin() && is_post_type_archive('product') && $query->is_main_query()) {
    $tax_query = array(
      'taxonomy' => 'badge-tags',
      'field'    => 'slug',
      'terms'    => 'coming-soon',
      'operator' => 'NOT IN',
    );
    $query->set('tax_query', array($tax_query));
  }
}
add_action('pre_get_posts', 'exclude_coming_soon_from_shop');

// add subscription auto renewal information with custom action hooks
add_action('subscription_before_buy_buttons', 'custom_markup_before_buy_buttons', 10);
function custom_markup_before_buy_buttons() {
    echo '<div class="auto-renewal__container" data-component="auto-renewal-consent">
            <p class="has-md-font-size"><strong>Automatic Renewal for your convenience.</strong></p>
            <p>Subscription will automatically renew each year. You can opt out of auto renewal at any time through your account or by calling <span class="auto-renew__phone">1-800-333-9037.</span></p>
            <div class="auto-renewal__consent">
              <input type="checkbox" id="auto-renewal__consent--input">
              <label for="auto-renewal__consent--input" class="auto-renewal__consent--label">I understand and agree to the <a class="auto-renewal__modal--link" href="javascript:void(0);" aria-describedby="adtl-info" aria-controls="auto-renewal-information">Auto Renew Terms and Conditions<span id="adlt-info" class="sr-only">Open additional auto renewal information modal</span></a> I authorize Mayo Clinic Health Letter to charge my account every 12 months until canceled, and I agree to receive my automatic renewal reminders via electronic mail.</label>
              <div id="auto-renewal-information" class="modal modal--animate-scale js-modal" data-component="modal">
                <div class="modal__content" role="alertdialog" aria-labelledby="auto-renewal-information" aria-describedby="auto-renewal-information">
                  <p class="has-heading-lg-font-size auto-renewal__modal--title">Auto Renew Terms</p>
                  <p class="auto-renewal__modal--text"><strong>Automatic Renewal for your convenience.</strong> With your credit card payment today, <strong>you agree to a continuous subscription that will be automatically renewed at the end of each term at the renewal rate and term then in effect, unless you cancel.</strong> You will receive a reminder with your renewal rate 4-6 weeks in advance of your subscription expiration. At the end of your current term, if you provided your credit/debit card, you authorize us to charge it for your renewal, or we will bill you if your card can’t be charged. <strong>You may cancel or manage your subscription at any time through your online account or by calling <span class="auto-renew__phone">1-800-333-9037.</span> A refund will be processed to the credit card where purchase was made. For any questions, please call <span class="auto-renew__phone">1-800-333-9037</span> or send an email to</strong> <a href="mailto:customerservice@mayopublications.com">customerservice@mayopublications.com</a></p>
                  <a href="javascript:void(0);" class="reset js-modal__close">' . __('Close', 'mayo') . '</a>
                  <a href="javascript:void(0);" class="reset modal__close-btn modal__close-btn--text js-modal__close">' . __('Close', 'mayo') . '</a>
                </div>
              </div>
            </div>';
}

add_action('subscription_after_buy_buttons', 'custom_markup_after_buy_buttons', 10);
function custom_markup_after_buy_buttons() {
    echo '</div>';
}

// check if customer has active subscription
function user_has_active_subscription_to_product($user_id, $product_id) {
  // Get all the product variations
  $product = wc_get_product($product_id);
  $variations = $product->get_children();

  // Check if user has an active subscription to any variation
  foreach ($variations as $variation_id) {
      if (wcs_user_has_subscription($user_id, $variation_id, 'active')) {
          return true;
      }
  }

  return false;
}

/**
 * Buy now functionality
 */
// save existing cart and only proceed to checkout with buy now product
function direct_checkout_function() {
  if (!isset($_GET['buy_now']) || !is_numeric($_GET['buy_now'])) {
    return;
  }

  $product_id = (int) $_GET['buy_now'];
  $original_cart = WC()->cart->get_cart_contents();

  // Store original cart into session
  WC()->session->set('saved_cart', $original_cart);
  // Empty the cart
  WC()->cart->empty_cart();
  // Add the product
  WC()->cart->add_to_cart($product_id);

  // Redirect to checkout
  wp_redirect(wc_get_checkout_url());
  exit;
}
add_action('template_redirect', 'direct_checkout_function');

// restore cart on any page except buy now and thank you page
function restore_cart_after_direct_checkout() {
  if (!isset($_GET['buy_now']) && !is_checkout() && !is_wc_endpoint_url('order-received')) {
    // Check for saved cart and load it, keeping buy now product
    $saved_cart = WC()->session->get('saved_cart');
    if (!empty($saved_cart)) {
      foreach ($saved_cart as $cart_item_key => $cart_item) {
        WC()->cart->add_to_cart($cart_item['product_id'], $cart_item['quantity'], $cart_item['variation_id'], $cart_item['variation'], $cart_item['data']);
      }
      WC()->session->__unset('saved_cart');
    }
  }
}
add_action('template_redirect', 'restore_cart_after_direct_checkout');

// on thank you page, wait for template to clear cart then restore saved cart
function restore_cart_after_order_placed($order_id) {
  // Check for saved cart and load it
  $saved_cart = WC()->session->get('saved_cart');
  if (!empty($saved_cart)) {
    foreach ($saved_cart as $cart_item_key => $cart_item) {
      WC()->cart->add_to_cart($cart_item['product_id'], $cart_item['quantity'], $cart_item['variation_id'], $cart_item['variation'], $cart_item['data']);
    }
    WC()->session->__unset('saved_cart');
  }
}
add_action('woocommerce_thankyou', 'restore_cart_after_order_placed');
