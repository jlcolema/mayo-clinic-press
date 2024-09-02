<?php
/**
 * Onboard Form
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// redirect if not logged in
if ( !is_user_logged_in() ) {
  wp_redirect( home_url( '/my-account' ) );
  exit;
}

$current_user = wp_get_current_user();
$current_step = 2;
$living_well_id = 327;
$user_id = $current_user->ID;

// check submitted form data
if (isset($_POST['submit_selected_categories'])) {
  $selected_categories = isset($_POST['categories']) ? (array) $_POST['categories'] : [];
  $selected_categories[] = $living_well_id;
  update_user_meta($user_id, 'selected_categories', $selected_categories);
  update_user_meta($user_id, 'has_personalized', true);
  $current_step = 3;
}
if (isset($_POST['submit_personal_info'])) {
  $birthday = sanitize_text_field($_POST['birthday']);
  $gender = sanitize_text_field($_POST['gender']);
  update_user_meta($user_id, 'birthday', $birthday);
  update_user_meta($user_id, 'gender', $gender);
  $current_step = 4;

  // Check if newsletter is opted in
  if (isset($_POST['newsletter_general'])) {
    $user_email = $current_user->user_email;
    $user_first = $current_user->first_name;
    $user_last = $current_user->last_name;
    
    $selected_categories_ids = get_user_meta($user_id, 'selected_categories', true);
    $selected_categories_slugs = array();
    foreach ($selected_categories_ids as $category_id) {
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
      'resubscribe' => 'on',
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
}

if ($current_step == 2) {
  update_user_meta( $user_id, 'selected_categories', array($living_well_id) );
}

$standard_categories_ids = [322, 323, 324, 325, 326, 255, 327, 295, 328, 319, 259, 329, 317, 257];
$categories = get_terms([
  'taxonomy' => 'category',
  'hide_empty' => false,
  'include' => $standard_categories_ids,
]);
?>
<style>.singular{background-color:var(--color-contrast-low-alpha);}</style>
<div class="form-login-container">
  <div class="form-login-heading">
    <p class="heading--xs"><?php echo __('Set up account details'); ?></p>
    <h2>Welcome, <?php echo esc_html( $current_user->first_name ); ?>!</h2>
  </div>
  <div class="form-login-box register">
    <div class="form-login-register">
      <div class="onboarding__step-2"<?php if ($current_step !== 2) : ?> style="display: none;"<?php endif; ?>>
        <h3>Select the topics that interest you</h3>
        <p>Select as many topics that interest you from the list below. We've preselected Living Well to provide you with general health content.</p>
        <p>We'll use your selections to serve you relevant content and recommendations. You can update your selected topics at any time in My Account.</p>
        
        <form method="post" class="woocommerce-form woocommerce-form-register register">
          <div class="select-topics__container">
            <?php foreach ($categories as $category) :
              $is_living_well = ($category->term_id == $living_well_id);
            ?>
              <label>
                <input type="checkbox" name="categories[]" value="<?php echo esc_attr($category->term_id); ?>" <?php if ($is_living_well) echo 'checked disabled'; ?>>
                <div class="icon">
                  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="plus"><path d="m1 11h22v2h-22z"/><path d="m11 1h2v22h-2z"/></svg>
                  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="check"><path d="m7.29 21.27-7-6.99 1.42-1.42 5.58 5.58 15-15 1.42 1.41z"/></svg>
                </div>
                <?php echo esc_html($category->name); ?>
              </label>
            <?php endforeach; ?>
          </div>
          <button type="submit" name="submit_selected_categories" class="btn btn--primary">Save & Continue</button>
        </form>
      </div>

      <div class="onboarding__step-3"<?php if ($current_step !== 3) : ?> style="display: none;"<?php endif; ?>>
        <h3>Tell Us About Yourself</h3>
        <p>This optional information will help us provide more relevant content and recommendations.</p>

        <form method="post" class="woocommerce-form woocommerce-form-register register" id="form491">
          <input value="MKT.MCPress.MVP.EmailSignup.Sp-1705503620866" type="hidden" name="elqFormName">
          <input value="74881809" type="hidden" name="elqSiteId">
          <input value="" type="hidden" id="elqFormSubmissionToken" name="elqFormSubmissionToken">
          <input name="elqCampaignId" type="hidden">
          <input type="hidden" name="page_url" id="fe4562" value="">

          <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="birthday"><?php esc_html_e( 'Birthday', 'mayo' ); ?>&nbsp;<span>(optional)</span></label>
            <input type="date" id="birthday" name="birthday">
          </p>

          <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide" data-component="select-format">
            <label for="gender"><?php esc_html_e( 'Gender', 'mayo' ); ?>&nbsp;<span>(optional)</span></label>
            <select id="gender" name="gender">
                <option value=""></option>
                <option value="female">Female</option>
                <option value="male">Male</option>
                <option value="nonbinary">Non-binary</option>
                <option value="prefer-not-to-say">Prefer not to say</option>
            </select>
          </p>

          <div id="elq-FormLastRow" class="row">
            <input type="text" value="" tabindex="-1" autocomplete="off" style="width:100%;" class="elq-item-input" name="onboardSubmit">
          </div>

          <div class="newsletter__container">
            <p class="as-label">Sign up for our newsletter <span>(optional)</span></p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
              <label>
                <input type="checkbox" name="newsletter_general" value="newsletter_general">
                <span>Yes, I would like to receive content about health conditions, tips, books, and exclusive offers.</span>
              </label>
            </p>

            <a class="newsletter__modal--link" href="javascript:void(0);" aria-describedby="additional-newsletter-info-modal" aria-controls="additional-information">
              Learn more about Mayo Clinic’s use of data
              <span id="additional-newsletter-info-modal" class="sr-only">Open additional information modal</span>
            </a>
          </div>

          <button type="submit" name="submit_personal_info" class="btn btn--primary">Complete Account Setup</button>
        </form>
      </div>

      <div class="onboarding__step-4"<?php if ($current_step !== 4) : ?> style="display: none;"<?php endif; ?>>
        <h3>Congratulations, your account has been created!</h3>
        <p>A confirmation email has been sent to <?php echo esc_html( $current_user->user_email ); ?>.</p>

        <div class="buttons__container">
          <a href="<?php echo add_query_arg('show_tour', 'true', home_url()); ?>" class="btn btn--primary"><?php echo __('Go to Homepage', 'mayo'); ?></a>
          <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="btn"><?php echo __('View Your Account', 'mayo'); ?></a>
        </div>
      </div>
    </div>
    <div class="form-box-divider"></div>
    <div class="form-login-returning">
      <div class="returning-info">
        <div class="registration__timeline">
          <p class="step">
            <span class="step__number">
              <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="m10.02 27.39-8.69-8.7 1.41-1.41 7.28 7.28 19.27-19.27 1.42 1.42z"/></svg>
            </span>
            <span class="step__text">
              <?php echo __('Account Credentials', 'mayo'); ?>
            </span>
          </p>
          <p class="step<?php if ($current_step == 2) : ?> active<?php endif; ?>">
            <span class="step__number">
              <?php if ($current_step > 2) : ?>
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="m10.02 27.39-8.69-8.7 1.41-1.41 7.28 7.28 19.27-19.27 1.42 1.42z"/></svg>
              <?php else : ?>
                2
              <?php endif; ?>
            </span>
            <span class="step__text">
              <?php echo __('Select Topics', 'mayo'); ?>
            </span>
          </p>
          <p class="step<?php if ($current_step == 3) : ?> active<?php endif; ?>">
            <span class="step__number">
              <?php if ($current_step > 3) : ?>
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="m10.02 27.39-8.69-8.7 1.41-1.41 7.28 7.28 19.27-19.27 1.42 1.42z"/></svg>
              <?php else : ?>
                3
              <?php endif; ?>
            </span>
            <span class="step__text">
              <?php echo __('Personal Information', 'mayo'); ?>
            </span>
          </p>
          <p class="step<?php if ($current_step == 4) : ?> active<?php endif; ?>">
            <span class="step__number">
              4
            </span>
            <span class="step__text">
              <?php echo __('Complete!', 'mayo'); ?>
            </span>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (isset($_POST['submit_selected_categories'])) : ?>
  <script>
    document.onload = handleDocumentLoad('form491','74881809');
    function handleDocumentLoad(b,a){
      window.getElqFormSubmissionToken(b,a);
      window.processLastFormField(b);
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
<?php endif; ?>

<?php if ($current_step == 4) : ?>
  <script>
    const setCookie = (name, value, days) => {
      const date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      const expires = `; expires=${date.toUTCString()}`;
      document.cookie = `${name}=${value}${expires}; path=/`;
    };
    setCookie('seenPersonalizedOptionsModal', 'true', 36500);
  </script>
<?php endif; ?>