<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/lost-password-confirmation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;

wc_print_notice( wp_kses( __( 'If you have an account with us, a password reset email has been sent. If you didn\'t receive one, please ensure you have created an <a href="/my-account/">account</a> with us and check your Spam or Junk folder. If you need another reset email, please wait ten minutes before reattempting.', 'woocommerce' ), array( 'a' => array( 'href' => array() ) ) ) );
?>

<?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>

<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>
