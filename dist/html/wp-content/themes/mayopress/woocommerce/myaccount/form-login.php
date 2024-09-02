<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$enable_guest_checkout = get_option( 'woocommerce_enable_guest_checkout' ) == 'yes';
$URL_ARG = 'redirect_to_checkout';
$guest_checkout_url = apply_filters( 'wc_force_auth_checkout_page_url', wc_get_checkout_url() );

$create_account_btn_text = 'Create an Account';
$tag = 'button';
$guest_checkout_link = '';
if ( isset( $_GET[ $URL_ARG ] ) && $enable_guest_checkout ) {
  $create_account_btn_text = 'Continue as Guest';
  $tag = 'a';
  $guest_checkout_link = ' href="' . $guest_checkout_url . '?guest_checkout"';
}

$show_registration_form = (isset($_SESSION['registration_attempt']) && $_SESSION['registration_attempt']) || isset( $_GET[ 'registration' ] );
$_SESSION['registration_attempt'] = false;
?>
<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
<style>.singular{background-color:var(--color-contrast-low-alpha);}</style>
<div class="form-login-container">
  <?php if ( !isset( $_GET[ $URL_ARG ] ) || !$enable_guest_checkout ) : ?>
  <div class="form-login-heading">
    <h2>Sign in or create an account</h2>
  </div>
  <?php endif; ?>
  <div class="form-login-box<?php if ($show_registration_form) : ?> register<?php endif ?>" data-component="login-switches">
    <div class="form-login-register">
      <div class="new-customer-info">
        <?php if ( isset( $_GET[ $URL_ARG ] ) && $enable_guest_checkout ) : ?>
          <h3>New Customer</h3>
        <?php else : ?>
          <h3>New User</h3>
        <?php endif; ?>

        <?php if ( !isset( $_GET[ $URL_ARG ] ) || !$enable_guest_checkout ) : ?>
        <p>For a more personalized experience, create an account.</p>
        <?php endif; ?>

        <<?php echo $tag . $guest_checkout_link; ?> class="btn<?php if ( isset( $_GET[ $URL_ARG ] ) && $enable_guest_checkout ) : ?> btn--primary<?php endif; ?> register-btn">
          <?php echo $create_account_btn_text; ?>
        </<?php echo $tag; ?>>

        <?php if ( !isset( $_GET[ $URL_ARG ] ) || !$enable_guest_checkout ) : ?>
        <p>The benefits of having an account include:</p>
        <ul>
          <li><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7.29 21.27-7-6.99 1.42-1.42 5.58 5.58 15-15 1.42 1.41z"/></svg>Faster checkout</li>
          <li><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7.29 21.27-7-6.99 1.42-1.42 5.58 5.58 15-15 1.42 1.41z"/></svg>Order tracking</li>
          <li><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7.29 21.27-7-6.99 1.42-1.42 5.58 5.58 15-15 1.42 1.41z"/></svg>Receive personalized content</li>
          <li><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7.29 21.27-7-6.99 1.42-1.42 5.58 5.58 15-15 1.42 1.41z"/></svg>Save content to build your library</li>
        </ul>
        <?php else : ?>
        <p>You will have the opportunity to create an account in checkout.</p>
        <?php endif; ?>
      </div>
      <div class="form-register-form">
        <p>Mayo Clinic Press will not share your email address with third parties and will only send you content relevant to your selected topics.</p>
        <h3>New User</h3>

        <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

          <?php do_action( 'woocommerce_register_form_start' ); ?>

          <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
              <label for="reg_username"><?php esc_html_e( 'Username', 'mayo' ); ?>&nbsp;<span class="required">*</span></label>
              <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
            </p>

          <?php endif; ?>

          <p class="woocommerce-form-row form-row form-row-first">
            <label for="reg_first_name"><?php esc_html_e( 'First Name', 'mayo' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="first_name" id="reg_first_name" autocomplete="given-name" value="<?php if ( ! empty( $_POST['first_name'] ) ) echo esc_attr( wp_unslash( $_POST['first_name'] ) ); ?>" required />
          </p>

          <p class="woocommerce-form-row form-row form-row-last">
            <label for="reg_last_name"><?php esc_html_e( 'Last Name', 'mayo' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="last_name" id="reg_last_name" autocomplete="family-name" value="<?php if ( ! empty( $_POST['last_name'] ) ) echo esc_attr( wp_unslash( $_POST['last_name'] ) ); ?>" required />
          </p>

          <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="reg_email"><?php esc_html_e( 'Email address', 'mayo' ); ?>&nbsp;<span class="required">*</span><a class="privacy__modal--link" href="javascript:void(0);" aria-describedby="privacy-info-modal" aria-controls="privacy-information">Why are you asking me for this?</a></label>
            <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>

            <div id="privacy-information" class="modal modal--animate-scale js-modal" data-component="modal">
              <div class="modal__content" role="alertdialog" aria-labelledby="privacy-information" aria-describedby="privacy-information">
                <p>You will use your email address to login to your account. In the following steps you will be able to choose what email communications you receive from us. We will not share your email address with third parties. Learn more about Mayo Clinic’s <a href="/privacy-policy/" target="_blank">Privacy Policy</a>.</p>
                <a href="javascript:void(0);" class="reset modal__close-btn modal__close-btn--text js-modal__close"><?php echo __('Close', 'mayo'); ?></a>
              </div>
            </div>
          </div>

          <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
              <label for="reg_password"><?php esc_html_e( 'Create a password', 'mayo' ); ?>&nbsp;<span class="required">*</span></label>
              <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
            </p>

            <p class="password-support">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 23a11 11 0 1 1 11-11 11 11 0 0 1 -11 11zm0-20a9 9 0 1 0 9 9 9 9 0 0 0 -9-9zm1 15h-2v-8h2zm0-10h-2v-2h2z"/></svg> A strong password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ & ).
            </p>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
              <label for="reg_confirm_password"><?php esc_html_e( 'Re-enter password', 'mayo' ); ?>&nbsp;<span class="required">*</span></label>
              <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="confirm_password" id="reg_confirm_password" autocomplete="new-password" />
            </p>

          <?php else : ?>

            <p><?php esc_html_e( 'A password will be sent to your email address.', 'mayo' ); ?></p>

          <?php endif; ?>

          <p class="woocommerce-form-row">
            <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
            <button type="submit" class="btn btn--primary register" name="register" value="<?php esc_attr_e( 'Create Account', 'mayo' ); ?>"><?php esc_html_e( 'Create Account', 'mayo' ); ?></button>
          </p>

          <?php do_action( 'woocommerce_register_form_end' ); ?>

        </form>
      </div>
    </div>
    <div class="form-box-divider"></div>
    <div class="form-login-returning">
      <div class="returning-info">
        <div class="registration__timeline">
          <p class="step active"><span class="step__number">1</span> <span class="step__text"><?php echo __('Account Credentials', 'mayo'); ?></span></p>
          <p class="step"><span class="step__number">2</span> <span class="step__text"><?php echo __('Select Topics', 'mayo'); ?></span></p>
          <p class="step"><span class="step__number">3</span> <span class="step__text"><?php echo __('Personal Information', 'mayo'); ?></span></p>
          <p class="step"><span class="step__number">4</span> <span class="step__text"><?php echo __('Complete!', 'mayo'); ?></span></p>
        </div>
      </div>
      <div class="form-signin-form">
        <?php if ( isset( $_GET[ $URL_ARG ] ) && $enable_guest_checkout ) : ?>
          <h3>Returning Customer</h3>
        <?php else : ?>
          <h3>Returning User</h3>
        <?php endif; ?>
        <form class="woocommerce-form woocommerce-form-login login" method="post">

          <?php do_action( 'woocommerce_login_form_start' ); ?>

          <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="username"><?php esc_html_e( 'Username or email address', 'mayo' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
          </p>
          <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="password"><?php esc_html_e( 'Password', 'mayo' ); ?>&nbsp;<span class="required">*</span></label>
            <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
          </p>

          <?php do_action( 'woocommerce_login_form' ); ?>

          <div class="form-row">
            <?php
            /**
             * IMPORTANT: Button below should go before label. It's by design.
             */
            ?>
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme remeber-me">
              <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'mayo' ); ?></span>
            </label>
            <span class="login-submit">
              <button type="submit" class="btn btn--primary sign-in" name="login" value="<?php esc_attr_e( 'Log in', 'mayo' ); ?>"><?php esc_html_e( 'Sign In', 'mayo' ); ?></button>

              <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

              <span class="woocommerce-LostPassword lost_password">
                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'mayo' ); ?></a>
              </span>
            </span>
          </div>
          <?php do_action( 'woocommerce_login_form_end' ); ?>

        </form>
      </div>
    </div>
  </div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
