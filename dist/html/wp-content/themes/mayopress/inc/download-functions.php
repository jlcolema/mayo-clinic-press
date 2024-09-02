<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function healthletter_subscribe_check( $order_id ) {
  if ( !$order_id ) {
    return;
  }
  if ( get_post_meta( $order_id, '_thankyou_emag_check_action_done', true ) ) {
    return;
  }
  // Lets grab the order
  $order = wc_get_order( $order_id );
  $order->update_meta_data( '_thankyou_emag_check_action_done', true );
  $order->save();

  $line_items = $order->get_items();

  // This loops over line items
  $have_digital_healthletter = false;
  foreach ( $line_items as $item ) {
    $product = $item->get_product();
    $sku = $product->get_sku();
    // LCHLARC LCHLACC - print and digital / LDHLARD LDHLACD - digital
    if ($sku == 'LCHLARC' || $sku == 'LCHLACC' || $sku == 'LDHLARD' || $sku == 'LDHLACD')
    {
      $have_digital_healthletter = true;
    }
  }
  if ( $have_digital_healthletter ) {
    $user = $order->get_user();

    $data = [
      'Key' => '080b65cd-f62d-478e-869e-fcc4cff12724',
      'ProgramKey' => 'mayo_health_letter_cws_instant_back_sub',
      'Email' => $user->user_email,
      'FirstName' => $user->user_firstname,
    ];
    $url = 'https://dfapi.emagazines.com/subscribe';

    $ch = curl_init( $url );
    $payload = json_encode( $data );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec($ch);
    curl_close($ch);
  }
}
add_action( 'woocommerce_thankyou_downloads', 'healthletter_subscribe_check' );
