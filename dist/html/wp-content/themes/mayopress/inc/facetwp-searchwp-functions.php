<?php
/**
 * FACETWP
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// add query_var for custom query parameters
function add_query_var() {
  global $wp;
  $wp->add_query_var('view');
  $wp->add_query_var('_products_articles');
  $wp->add_query_var('_listing_categories');
  $wp->add_query_var('_categories');
}

add_action('init','add_query_var');

add_filter( 'facetwp_preload_url_vars', function( $url_vars ) {
  // auto check format for listing pages
  if ( 'videos' == FWP()->helper->get_uri() ) {
    $url_vars['listing_format'] = [ 'video' ];
  } elseif ( 'podcasts' == FWP()->helper->get_uri() ) {
    $url_vars['listing_format'] = [ 'podcast' ];
  }
  return $url_vars;
});

add_filter( 'pre_get_posts', function( $query ) {
  if ( 'ids' == $query->get( 'fields' ) ) {
      $query->set( 'facetwp', false ); // force FacetWP to ignore the query
  }
}, 500 );

/**
 * scroll to top when facetwp pager interacted with
 */
add_action( 'wp_head', function() { ?>
  <script>
    (function($) {
      $(document).on('facetwp-refresh', function() {
        if (FWP.soft_refresh == true) {
          FWP.enable_scroll = true;
        } else {
          FWP.enable_scroll = false;
        }
      });
      $(document).on('facetwp-loaded', function() {
        if (FWP.enable_scroll == true) {
          $('html, body').animate({
            scrollTop: $("#full-list").length ? $("#full-list").offset().top : 0
          }, 500);
        }
      });
    })(jQuery);
  </script>
<?php } );

// auto expand subcategories
add_action( 'wp_footer', function() {
  ?>
    <script type="text/javascript">
      (function($) {
        document.addEventListener( "facetwp-loaded", function() {
          $( ".facetwp-checkbox.checked .facetwp-expand" ).trigger( "click" );
        });
      })(jQuery)    
    </script>
  <?php
}, 100);

// replace expand/collaps icons
add_filter( 'facetwp_assets', function( $assets ) {
  FWP()->display->json['expand'] = '<span>+</span>';
  FWP()->display->json['collapse'] = '<span>-</span>';

  // Return the same thing (since we're hijacking this hook)
  return $assets;
});

add_filter( 'facetwp_facet_display_value', function( $label, $params ) {
  if ( 'on_sale' == $params['facet']['name'] ) {
    $label = $params['facet']['label'];
  }
  return $label;
}, 20, 2 );


/**
 * SEARCHWP
 */
// remove searchwp post limit
add_filter( 'searchwp\swp_query\args', function( $args ) {
  if ( isset( $args['facetwp'] ) ) {
      $args['posts_per_page'] = -1;
  }
  return $args;
} );
