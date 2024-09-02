<?php
/**
 * Subscription details table
 *
 * @author  Prospress
 * @package WooCommerce_Subscription/Templates
 * @since 1.0.0 - Migrated from WooCommerce Subscriptions v2.2.19
 * @version 1.0.0 - Migrated from WooCommerce Subscriptions v2.6.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<table class="shop_table subscription_details">
	<tbody class="is-hidden">
		<tr>
			<td><?php esc_html_e( 'Status', 'woocommerce-subscriptions' ); ?></td>
			<td><?php echo esc_html( wcs_get_subscription_status_name( $subscription->get_status() ) ); ?></td>
		</tr>
		<?php do_action( 'wcs_subscription_details_table_before_dates', $subscription ); ?>
		<?php
		$dates_to_display = apply_filters( 'wcs_subscription_details_table_dates_to_display', array(
			'start_date'              => _x( 'Start date', 'customer subscription table header', 'woocommerce-subscriptions' ),
			'last_order_date_created' => _x( 'Last order date', 'customer subscription table header', 'woocommerce-subscriptions' ),
			'next_payment'            => _x( 'Next payment date', 'customer subscription table header', 'woocommerce-subscriptions' ),
			'end'                     => _x( 'End date', 'customer subscription table header', 'woocommerce-subscriptions' ),
			'trial_end'               => _x( 'Trial end date', 'customer subscription table header', 'woocommerce-subscriptions' ),
		), $subscription );
		foreach ( $dates_to_display as $date_type => $date_title ) : ?>
			<?php $date = $subscription->get_date( $date_type ); ?>
			<?php if ( ! empty( $date ) ) : ?>
				<tr>
					<td><?php echo esc_html( $date_title ); ?></td>
					<td><?php echo esc_html( $subscription->get_date_to_display( $date_type ) ); ?></td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php do_action( 'wcs_subscription_details_table_after_dates', $subscription ); ?>

		<?php if ( $subscription->get_status() != 'cancelled' ) : ?>
			<tr>
				<td><?php esc_html_e( 'Auto renew', 'woocommerce-subscriptions' ); ?></td>
				<td>
					<div class="wcs-auto-renew-toggle">
						<?php

						$toggle_classes = array( 'auto-renew-toggle', 'auto-renew-toggle--hidden' );

						if ( $subscription->is_manual() ) {
							$toggle_label     = __( 'Enable auto renew', 'woocommerce-subscriptions' );
							$toggle_classes[] = 'auto-renew-toggle--off';
						} else {
							$toggle_label     = __( 'Disable auto renew', 'woocommerce-subscriptions' );
							$toggle_classes[] = 'auto-renew-toggle--on';
						}?>
						<a href="javascript:void(0);" class="<?php echo esc_attr( implode( ' ' , $toggle_classes ) ); ?>" aria-controls="auto-renew-toggle">
							<span class="sr-only"><?php echo esc_html( $toggle_label ) ?></span>
							<i class="subscription-auto-renew-toggle__i" aria-hidden="true">
								<svg class="icon icon--check icon--xxxs" aria-hidden="true" focusable="false">
									<use xlink:href="#check"></use>
								</svg>
							</i>
						</a>


						<a class="auto-renewal__modal--link" href="javascript:void(0);" aria-controls="auto-renewal-information">
							<span class="sr-only">Open additional auto renewal information modal</span>
							Auto Renew Terms
						</a>
						<div id="auto-renewal-information" class="modal modal--animate-scale js-modal" data-component="modal">
							<div class="modal__content" role="alertdialog" aria-labelledby="auto-renewal-information" aria-describedby="auto-renewal-information">
								<p class="has-heading-lg-font-size auto-renewal__modal--title">Auto Renew Terms</p>
								<p class="auto-renewal__modal--text"><strong>Automatic Renewal for your convenience.</strong> With your credit card payment today, <strong>you agree to a continuous subscription that will be automatically renewed at the end of each term at the renewal rate and term then in effect, unless you cancel.</strong> You will receive a reminder with your renewal rate 4-6 weeks in advance of your subscription expiration. At the end of your current term, if you provided your credit/debit card, you authorize us to charge it for your renewal, or we will bill you if your card can’t be charged. <strong>You may cancel or manage your subscription at any time through your online account or by calling 1-800-333-9037. A refund will be processed to the credit card where purchase was made. For any questions, please call 1-800-333-9037 or send an email to</strong> <a href="mailto:customerservice@mayopublications.com">customerservice@mayopublications.com</a></p>
								<a href="javascript:void(0);" class="reset js-modal__close"><?php echo __('Close', 'mayo'); ?></a>
								<a href="javascript:void(0);" class="reset modal__close-btn modal__close-btn--text js-modal__close"><?php echo __('Close', 'mayo'); ?></a>
							</div>
						</div>
					</div>
				</td>
			</tr>
		<?php endif; ?>

		<?php do_action( 'wcs_subscription_details_table_before_payment_method', $subscription ); ?>
		<?php do_action( 'woocommerce_subscription_before_actions', $subscription ); ?>
		<?php $actions = wcs_get_all_user_actions_for_subscription( $subscription, get_current_user_id() ); ?>
		<?php if ( ! empty( $actions ) ) : ?>
			<tr>
				<td class="subscription--actions-label"><?php esc_html_e( 'Actions', 'woocommerce-subscriptions' ); ?></td>
				<td>
					<div class="subscription--actions">
						<?php
							$reversed_actions = array_reverse( $actions );
							foreach ( $reversed_actions as $key => $action ) :
								$action_name = esc_html( $action['name'] );
								$action_name_lowercase = strtolower( $action_name );
								$action_name_with_hyphens = str_replace( ' ', '-', $action_name_lowercase );
						?>
							<a href="javascript:void(0)" data-href="<?php echo esc_url( $action['url'] ); ?>" aria-controls="<?php echo $action_name_with_hyphens; ?>" class="btn <?php echo sanitize_html_class( $key ) ?>"><?php echo esc_html( $action['name'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</td>
			</tr>
		<?php endif; ?>
		<?php do_action( 'woocommerce_subscription_after_actions', $subscription ); ?>
	</tbody>
</table>
<div class="loading-spinner"></div>

<?php if ( $notes = $subscription->get_customer_order_notes() ) : ?>
	<h2><?php esc_html_e( 'Subscription updates', 'woocommerce-subscriptions' ); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ( $notes as $note ) : ?>
		<li class="woocommerce-OrderUpdate comment note">
			<div class="woocommerce-OrderUpdate-inner comment_container">
				<div class="woocommerce-OrderUpdate-text comment-text">
					<p class="woocommerce-OrderUpdate-meta meta"><?php echo esc_html( date_i18n( _x( 'l jS \o\f F Y, h:ia', 'date on subscription updates list. Will be localized', 'woocommerce-subscriptions' ), wcs_date_to_time( $note->comment_date ) ) ); ?></p>
					<div class="woocommerce-OrderUpdate-description description">
						<?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
					</div>
	  				<div class="clear"></div>
	  			</div>
				<div class="clear"></div>
			</div>
		</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>
