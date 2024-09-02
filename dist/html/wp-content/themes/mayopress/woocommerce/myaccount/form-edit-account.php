<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

$user_id = $user->ID;
$user_first = $user->first_name;
$user_last = $user->last_name;
$user_email = $user->user_email;
$birthday = get_user_meta($user_id, 'birthday', true);
$gender = get_user_meta($user_id, 'gender', true);

if (isset($_POST['save_account_details'])) {    
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

do_action( 'woocommerce_before_edit_account_form' ); ?>
<h2 class="dashboard__title"><?php echo _e('Account Details', 'mayo'); ?></h2>
<form id="form491" class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >
	<input value="MKT.MCPress.MVP.EmailSignup.Sp-1705503620866" type="hidden" name="elqFormName">
	<input value="74881809" type="hidden" name="elqSiteId">
	<input value="" type="hidden" id="elqFormSubmissionToken" name="elqFormSubmissionToken">
	<input name="elqCampaignId" type="hidden">
	<input type="hidden" name="page_url" id="fe4562" value="">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<p id="account-details-first" class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class=" " name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p id="account-details-last" class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></em></span>
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="birthday"><?php esc_html_e( 'Birthday', 'mayo' ); ?>&nbsp;<span>(optional)</span></label>
		<input type="date" id="birthday" name="birthday" value="<?php echo esc_attr($birthday); ?>">
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide" data-component="select-format">
    <label for="gender"><?php esc_html_e( 'Gender', 'mayo' ); ?>&nbsp;<span>(optional)</span></label>
    <select id="gender" name="gender">
        <option value=""></option>
        <option value="female" <?php selected($gender, 'female'); ?>>Female</option>
        <option value="male" <?php selected($gender, 'male'); ?>>Male</option>
        <option value="nonbinary" <?php selected($gender, 'nonbinary'); ?>>Non-binary</option>
        <option value="prefer-not-to-say" <?php selected($gender, 'prefer-not-to-say'); ?>>Prefer not to say</option>
    </select>
	</p>

	<div id="elq-FormLastRow" class="row">
		<input type="text" value="" tabindex="-1" autocomplete="off" style="width:100%;" class="elq-item-input" name="onboardSubmit">
	</div>

	<fieldset>

		<legend><h2 class="dashboard__title"><?php echo _e('Password Change', 'mayo'); ?></h2></legend>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
		</p>
	</fieldset>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button btn" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
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

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
