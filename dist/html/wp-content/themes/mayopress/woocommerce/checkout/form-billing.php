<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-billing-fields">
	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		$fields = $checkout->get_checkout_fields( 'billing' );

		foreach ( $fields as $key => $field ) {
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		}
		?>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields" data-component="create-guest-account">
		<p class="form-row form-row-wide create-account is-hidden">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
			</label>
		</p>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) :
  		$is_subscription = cart_contains_subscription();
		?>
			<div class="accordion js-accordion" data-animation="on" data-component="accordion">
				<div class="accordion__item js-accordion__item<?php if ($is_subscription) : ?> accordion__item--is-open<?php endif; ?>">
					<div class="accordion__header reset js-accordion__trigger js-tab-focus">
						<?php
							$text = __('optional', 'mayo');
							if ($is_subscription) {
								$text = __('required', 'mayo');
							}
							echo sprintf(
								'<p class="create-account__header"><strong>%s</strong> (%s)</p>',
								__('Create an Account', 'mayo'),
								$text
							);
						?>
						<svg class="icon icon--chevron-down" aria-hidden="true" focusable="false"><use xlink:href="#chevron-down"></use></svg>
					</div>
					<div class="accordion__panel js-accordion__panel">
						<div class="accordion__panel-content">
							<div class="create-account">
								<div class="create-account__content">
									<p class="create-account__text">
										<?php 
											if ($is_subscription) {
												echo __('Because you\'re purchasing the Health Letter, an account is required to manage your subscription.', 'mayo');
											} else {
												echo __('Register now for a faster checkout process on future purchases.', 'mayo');
											}
										?>
									</p>

									<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
										<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
									<?php endforeach; ?>

									<p class="create-account__password-tip">
										<?php echo __( 'A strong password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ & ).', 'mayo' ); ?>
									</p>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
