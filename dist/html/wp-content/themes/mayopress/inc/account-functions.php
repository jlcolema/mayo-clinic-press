<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// modify account navigation
add_filter ( 'woocommerce_account_menu_items', 'change_account_links' );

function change_account_links( $menu_links ){
  // Add "My Library"
  $my_library = array( 'my-library' => 'My Library' );

  // Insert "My Library" after "Dashboard"
  $menu_links = array_slice( $menu_links, 0, 1, true ) + $my_library + array_slice( $menu_links, 1, NULL, true );

  // Change label names
  $menu_links['downloads'] = 'My Library';
  $menu_links['orders'] = 'Order History';
  $menu_links['edit-account'] = 'Account Details';

  // Remove "Downloads" and log out
  unset( $menu_links['downloads'] );
  unset( $menu_links['customer-logout'] );

  return $menu_links;
}

// Register the "My Library" endpoint
function my_library_endpoint() {
  add_rewrite_endpoint('my-library', EP_ROOT | EP_PAGES);
}
add_action('init', 'my_library_endpoint');

// Add new content to the "My Library" endpoint
function my_library_content() {
  if (strpos($_SERVER['REQUEST_URI'], 'my-library/collection') !== false) {
      // Display "Collection" content
      echo do_shortcode('[cbxwpbookmark allowdelete="1" allowdeleteall="1"]');
      echo '<a href="/my-account/my-library" class="cbxwpbookmark__return"><svg class="icon icon--arrow-left" aria-hidden="true" focusable="false"><use xlink:href="#arrow-left"></use></svg> Return to My Library</a>';
  } else {
      // Display "My Library" content
      echo '<h2 class="dashboard__title">My Library</h2>';
      echo '<p class="">View and manage your Collections</p>';
      echo do_shortcode('[cbxwpbookmark-mycat base_url="/my-account/my-library/collection/" title="" allowedit="1" show_count="1"]');
  }
}
add_action('woocommerce_account_my-library_endpoint', 'my_library_content');

// update shipping
add_action( 'woocommerce_customer_save_address', 'my_get_wc_user_fields', 1, 2 );

function my_get_wc_user_fields( $user_id ) {
  if ( !$_POST['make_shipping_address'] ) {
    return;
  }

  $fields = array(
    'billing_address_1',
    'billing_address_2',
    'billing_city',
    'billing_company',
    'billing_country',
    'billing_email',
    'billing_first_name',
    'billing_last_name',
    'billing_phone',
    'billing_postcode',
    'billing_state',
  );

  $return = array();

  $meta = get_user_meta( $user_id );

  if ( ! empty ( $user_id ) && ! empty ( $meta ) ) {
    foreach ( $fields as $field ) {
      if ( is_array( $meta[ $field ] ) && ! empty( $meta[ $field ][0] ) ) {
        $key = explode('_', $field);
        $shipping_field = str_replace( 'billing', 'shipping', $field );

        if ( ! empty( $key[1] ) && is_array( $return[ $key[0] ] ) ) {
          $return[ $key[0] ][ $key[1] ] = $meta[ $field ][0];
        }

        $return[$shipping_field] = $meta[ $field ][0];
      }
    }
  }

  my_update_wc_user_fields( $user_id, $return );
}

function my_update_wc_user_fields( $user_id, $args = array() ) {
  if ( ! empty( $args ) ) {
    foreach ( $args as $key => $value ) {
      update_user_meta( $user_id, $key, $value );
    }

    // call the sync_customer_shipping_address function after updating the user meta.
    if ( function_exists( 'sync_customer_shipping_address' ) ) {
      sync_customer_shipping_address( $user_id, 'shipping' );
    }
  }
}


// update cancel subscription button text
function custom_subscription_actions( $actions, $subscription, $user_id ) {
  if ( isset( $actions['cancel'] ) ) {
    $actions['cancel']['name'] = __( 'Cancel Subscription', 'woocommerce-subscriptions' );
  }
  return $actions;
}
add_filter( 'wcs_view_subscription_actions', 'custom_subscription_actions', 10, 3 );

// replace copy text
function cbxwpbookmark_custom_translation( $translated_text, $text, $domain ) {
  if ( 'cbxwpbookmark' === $domain ) {
    switch ( $translated_text ) {
      case 'Once you delete, it\'s gone forever. You can not revert it back.':
        $translated_text = 'You\'ll need to re-add Saved Content.';
        break;
      case 'Category title':
        $translated_text = 'Collection Name';
        break;
      case 'Private Category':
        $translated_text = 'Private Collection';
        break;
      case 'Public Category':
        $translated_text = 'Public Collection';
        break;
      case 'Type Category Name':
        $translated_text = 'Type Collection Name';
        break;
      case 'Delete All':
        $translated_text = 'Remove All';
        break;
    }
  }
  return $translated_text;
}
add_filter( 'gettext', 'cbxwpbookmark_custom_translation', 20, 3 );

function cbx_ajax_scripts() {
  wp_enqueue_script('cbx-ajax-scripts', get_stylesheet_directory_uri() . '/src/js/utils/cbx-ajax-scripts.js', array('jquery'), '', true);
  // Localize script to pass PHP variables to JS
  wp_localize_script('cbx-ajax-scripts', 'cbxCustom', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('cbxbookmarknonce'),
  ));
}
add_action('wp_enqueue_scripts', 'cbx_ajax_scripts');

function delete_bookmarks_by_category() {
  check_ajax_referer('cbxbookmarknonce', 'security');

  $user_id = get_current_user_id();
  $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;

  if ($user_id == 0 || $cat_id == 0) {
    wp_send_json_error(array('msg' => 'Invalid user or category ID'));
  }

  global $wpdb;
  $cbxwpbookmark_table = $wpdb->prefix . 'cbxwpbookmark';

  $delete_status = $wpdb->delete($cbxwpbookmark_table, array('user_id' => $user_id, 'cat_id' => $cat_id), array('%d', '%d'));

  if ($delete_status !== false) {
    wp_send_json_success(array('msg' => 'Bookmarks deleted successfully', 'code' => 1));
  } else {
    wp_send_json_error(array('msg' => 'Failed to delete bookmarks'));
  }
}
add_action('wp_ajax_delete_bookmarks_by_category', 'delete_bookmarks_by_category');

function delete_bookmark_by_category_and_id() {
  check_ajax_referer('cbxbookmarknonce', 'security');

  $user_id = get_current_user_id();
  $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

  if ($user_id == 0 || $cat_id == 0 || $id == 0) {
    wp_send_json_error(array('msg' => 'Invalid user or category ID'));
  }

  global $wpdb;
  $cbxwpbookmark_table = $wpdb->prefix . 'cbxwpbookmark';

  $delete_status = $wpdb->delete($cbxwpbookmark_table, array('user_id' => $user_id, 'cat_id' => $cat_id, 'id' => $id), array('%d', '%d', '%d'));

  if ($delete_status !== false) {
    wp_send_json_success(array('msg' => 'Bookmarks deleted successfully', 'code' => 1));
  } else {
    wp_send_json_error(array('msg' => 'Failed to delete bookmarks'));
  }
}
add_action('wp_ajax_delete_bookmark_by_category_and_id', 'delete_bookmark_by_category_and_id');

// set registration session
add_action('init', 'start_custom_session', 1);
function start_custom_session() {
  if (!session_id()) {
    session_start();
  }
}

add_action('woocommerce_register_post', 'set_registration_attempt_flag', 10, 3);
function set_registration_attempt_flag($username, $email, $validation_errors) {
  $_SESSION['registration_attempt'] = true;
}

// matching password validation
add_filter('woocommerce_registration_errors', 'registration_errors_validation', 10,3);
function registration_errors_validation($reg_errors, $sanitized_user_login, $user_email) {
  global $woocommerce;
  extract( $_POST );
  if ( strcmp( $password, $confirm_password ) !== 0 ) {
    return new WP_Error( 'registration-error', __( 'Passwords do not match.', 'woocommerce' ) );
  }
  return $reg_errors;
}

// save user meta
add_action( 'woocommerce_created_customer', 'mayo_save_extra_register_fields' );
function mayo_save_extra_register_fields( $customer_id ) {
  if ( isset( $_POST['first_name'] ) ) {
    update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['first_name'] ) );
  }
  if ( isset( $_POST['last_name'] ) ) {
    update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['last_name'] ) );
  }
}

add_action( 'woocommerce_save_account_details', 'save_custom_user_account_fields' );
function save_custom_user_account_fields( $user_id ) {
  if ( isset( $_POST['birthday'] ) ) {
    update_user_meta( $user_id, 'birthday', sanitize_text_field( $_POST['birthday'] ) );
  }

  if ( isset( $_POST['gender'] ) ) {
    update_user_meta( $user_id, 'gender', sanitize_text_field( $_POST['gender'] ) );
  }
}


// redirect registration to onboarding page
add_filter( 'woocommerce_registration_redirect', 'redirection_after_registration', 10, 1 );
function redirection_after_registration( $redirection_url ) {
  $redirection_url = home_url('/my-account/registration/');

  return $redirection_url;
}

function custom_account_modal() {
  if (is_account_page()) {
    $user_id = get_current_user_id();
    $has_personalized = get_user_meta($user_id, 'has_personalized', true);

    if (!$has_personalized) {
      echo '<a class="customize-account__modal--link" href="javascript:void(0);" aria-describedby="customize-account" aria-controls="customize-account" style="display: none;" aria-hidden="true"></a>
            <div id="customize-account" class="modal modal--animate-scale js-modal modal--is-visible" data-component="modal">
              <div class="modal__content" role="alertdialog" aria-labelledby="customize-account" aria-describedby="customize-account">
                <p class="heading--xs">' . __('NEW', 'mayo') . '</p>
                <p class="heading--lg">' . __('Customize Your Account', 'mayo') . '</p>
                <p>By providing some additional information, you will enjoy a more personalized experience. Recommendations for content and books will reflect your areas of interest.</p>
                <p>You can update this information at any time.</p>
                <a href="/my-account/registration/" class="btn btn--primary">' . __('Get Started', 'mayo') . '</a>
                <a href="javascript:void(0);" class="reset js-modal__close">' . __('No, thank you', 'mayo') . '</a>
                <a href="javascript:void(0);" class="reset modal__close-btn modal__close-btn--text js-modal__close">' . __('Close', 'mayo') . '</a>
              </div>
            </div>';

      // add meta data to no longer display modal
      update_user_meta($user_id, 'has_personalized', true);
    }
  }
}
add_action('woocommerce_account_content', 'custom_account_modal');

function add_custom_endpoint_query_vars( $vars ) {
  $vars['my-topics'] = 'my-topics';
  return $vars;
}
add_filter( 'woocommerce_get_query_vars', 'add_custom_endpoint_query_vars' );

function add_custom_my_account_menu_item( $items ) {
  $items = array_slice( $items, 0, 1, true ) 
  + array( 'my-topics' => __('My Topics', 'mayo') )
  + array_slice( $items, 1, NULL, true );

  return $items;
}
add_filter( 'woocommerce_account_menu_items', 'add_custom_my_account_menu_item' );

function my_topics_content() {
  $current_user = wp_get_current_user();
  $user_id = $current_user->ID;
  $living_well_id = 327;
  echo '<div class="woocommerce-notices-wrapper"></div>
        <h2 class="dashboard__title">' . __('My Topics', 'mayo') . '</h2>

        <p class="dashboard__subtitle">Select as many topics that interest you from the list below. We\'ve preselected Living Well to provide you with general health content.</p>
        <p class="dashboard__subtitle with-border--bottom">We\'ll use your selections to serve you relevant content and recommendations. You can come back here anytime to update your selected topics.</p>';

  // check submitted form data
  if (isset($_POST['submit_selected_categories'])) {
    $selected_categories = isset($_POST['categories']) ? (array) $_POST['categories'] : [];
    $selected_categories[] = $living_well_id;
    update_user_meta($user_id, 'selected_categories', $selected_categories);

    $user_first = $current_user->first_name;
    $user_last = $current_user->last_name;
    $user_email = $current_user->user_email;
    $birthday = get_user_meta($user_id, 'birthday', true);
    $gender = get_user_meta($user_id, 'gender', true);
 
    $selected_categories_slugs = array();
    foreach ($selected_categories as $category_id) {
      $term = get_term($category_id);
      if (!is_wp_error($term) && !empty($term)) {
        $selected_categories_slugs[$term->slug] = 'on';
      }
    }

    $elqFormName = isset($_POST['elqFormName']) ? sanitize_text_field($_POST['elqFormName']) : '';
    $elqSiteId = isset($_POST['elqSiteId']) ? sanitize_text_field($_POST['elqSiteId']) : '';
    $elqFormSubmissionToken = isset($_POST['elqFormSubmissionToken']) ? sanitize_text_field($_POST['elqFormSubmissionToken']) : '';
    $elqCampaignId = isset($_POST['elqCampaignId']) ? sanitize_text_field($_POST['elqCampaignId']) : '';
    $elqCustomerGUID = isset($_POST['elqCustomerGUID']) ? sanitize_text_field($_POST['elqCustomerGUID']) : '';
    $page_url = isset($_POST['page_url']) ? sanitize_text_field($_POST['page_url']) : '';
    $onboardSubmit = isset($_POST['onboardSubmit']) ? sanitize_text_field($_POST['onboardSubmit']) : '';
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;

    $body = array(
      'emailAddress' => $user_email,
      'userFirst' => $user_first,
      'userLast' => $user_last,
      'elqFormName' => $elqFormName,
      'elqSiteId' => $elqSiteId,
      'elqFormSubmissionToken' => $elqFormSubmissionToken,
      'elqCampaignId' => $elqCampaignId,
      'elqCustomerGUID' => $elqCustomerGUID,
      'elqCookieWrite' => '0',
      'page_url' => $page_url,
      'birthday' => $birthday,
      'gender' => $gender,
      'onboardSubmit' => $onboardSubmit,
    ) + $selected_categories_slugs;

    $response = wp_remote_post('https://s74881809.t.eloqua.com/e/f2', array(
      'method' => 'POST',
      'body' => $body,
      'timeout' => 45,
      'headers' => array(
        'Content-Type' => 'application/x-www-form-urlencoded',
        'User-Agent' => $user_agent
      )
    ));

    if (is_wp_error($response)) {
      error_log('Error in submitting to Eloqua: ' . $response->get_error_message());
    }
  }

  $selected_categories = get_user_meta($user_id, 'selected_categories', true);
  if (!$selected_categories) {
    $selected_categories = array($living_well_id);
  }

  $standard_categories_ids = [322, 323, 324, 325, 326, 255, 327, 295, 328, 319, 259, 329, 317, 257];
  $categories = get_terms([
    'taxonomy' => 'category',
    'hide_empty' => false,
    'include' => $standard_categories_ids,
  ]);
?>
  <form id="form491" method="post" class="woocommerce-form my-topics__page">
		<input value="MKT.MCPress.MVP.EmailSignup.Sp-1705503620866" type="hidden" name="elqFormName">
		<input value="74881809" type="hidden" name="elqSiteId">
		<input value="" type="hidden" id="elqFormSubmissionToken" name="elqFormSubmissionToken">
		<input name="elqCampaignId" type="hidden">
		<input type="hidden" name="page_url" id="fe4562" value="">

    <div class="select-topics__container">
      <?php foreach ($categories as $category) :
        $is_living_well = ($category->term_id == $living_well_id);
      ?>
        <label>
          <input type="checkbox" name="categories[]" value="<?php echo esc_attr($category->term_id); ?>" <?php if ($is_living_well || in_array($category->term_id, $selected_categories)) echo 'checked'; ?> <?php if ($is_living_well) echo 'disabled'; ?>>
          <div class="icon">
            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="plus"><path d="m2 15h28v2h-28z"/><path d="m15 2h2v28h-2z"/></svg>
            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="check"><path d="m10.02 27.39-8.69-8.7 1.41-1.41 7.28 7.28 19.27-19.27 1.42 1.42z"/></svg>
          </div>
          <?php echo esc_html($category->name); ?>
        </label>
      <?php endforeach; ?>
    </div>

    <div id="elq-FormLastRow" class="row">
      <input type="text" value="" tabindex="-1" autocomplete="off" style="width:100%;" class="elq-item-input" name="onboardSubmit">
    </div>

    <button type="submit" name="submit_selected_categories" class="btn">Save Changes</button>
  </form>

  <script>
    document.onload = handleDocumentLoad('form491','74881809');
    function handleDocumentLoad(b,a){
      window.getElqFormSubmissionToken(b,a);
      processLastFormField(b);
    }
    function getElqFormSubmissionToken(g,c){
      var e=new XMLHttpRequest();
      var b=document.getElementById(g);
      if(b&&b.elements.namedItem("elqFormSubmissionToken")){
        var f="https://s74881809.t.eloqua.com/e/f2";
        var a=window.getHostName(f);
        a="https://"+a+"/e/formsubmittoken?elqSiteID="+c;
        if(a){
          e.onreadystatechange=function(){
            if(e.readyState===4){
              if(e.status===200){
                b.elements.namedItem("elqFormSubmissionToken").value=e.responseText
              } else{
                b.elements.namedItem("elqFormSubmissionToken").value=""
              }
            }
          };
          e.open("GET",a,true);
          e.send()
        } else{
          b.elements.namedItem("elqFormSubmissionToken").value=""
        }
      }
    }
    function getHostName(b){
      if(typeof window.URL==="function"){
        return new window.URL(b).hostname
      } else {
        var a=b.match(/:\/\/(www[0-9]?\.)?(.[^\/:]+)/i);
        if(a!==null&&a.length>2&&typeof a[2]==="string"&&a[2].length>0){
          return a[2]
        } else {
          return null
        }
      }
    }
    function processLastFormField(b) {
      var form = document.getElementById(b);
      var lastFormField = form.querySelector("#elq-FormLastRow");
      lastFormField.style.display = "none";
    }
  </script>

  <script type='text/javascript'><!--//
    var timerId = null, timeout = 5;
  //--></script>
  <script type='text/javascript'><!--//
    function WaitUntilCustomerGUIDIsRetrieved() {
      if (!!(timerId)) {
        if (timeout == 0) {
          return;
        }
        if (typeof this.GetElqCustomerGUID === 'function') {
          document.forms["MKT.MCPress.MVP.EmailSignup.Sp-1705503620866"].elements["elqCustomerGUID"].value = GetElqCustomerGUID();
          return;
        }
        timeout -= 1;
      }
      timerId = setTimeout("WaitUntilCustomerGUIDIsRetrieved()", 500);
      return;
    }
    window.onload = WaitUntilCustomerGUIDIsRetrieved;
    // _elqQ.push(['elqGetCustomerGUID']);
  //--></script>

  <script>
    const page_url = window.location.href;
    if (document.querySelector('#fe4562')) document.querySelector('#fe4562').value = page_url;
  </script>
<?php
  if (isset($_POST['submit_selected_categories'])) {
    echo '<script>
      const value = `; ${document.cookie}`;
      const parts = value.split(`; endTour=`);
      if (parts.length !== 2) window.location.href = `${window.location.origin}?show_tour=true`;
    </script>';
  }
}
add_action( 'woocommerce_account_my-topics_endpoint', 'my_topics_content' );
