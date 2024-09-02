<?php
function get_acf_field_ajax() {
  if ( isset( $_POST['field_name'] ) ) {
    $field_name = sanitize_text_field( $_POST['field_name'] );
    $acf_field_content = get_field( $field_name, 'external_settings' );

    if ( $acf_field_content ) {
      wp_send_json_success( $acf_field_content );
    } else {
      wp_send_json_error( 'Field not found.' );
    }
  }
  wp_die();
}
add_action( 'wp_ajax_get_acf_field', 'get_acf_field_ajax' );
add_action( 'wp_ajax_nopriv_get_acf_field', 'get_acf_field_ajax' );

function get_acf_group_ajax() {
  if ( isset( $_POST['group_name'] ) ) {
    $group_name = sanitize_text_field( $_POST['group_name'] );
    $acf_group = get_field( $group_name, 'banner_options' );

    if ( $acf_group ) {
      wp_send_json_success( $acf_group );
    } else {
      wp_send_json_error( 'Group not found.' );
    }
  }
  wp_die();
}
add_action( 'wp_ajax_get_acf_group', 'get_acf_group_ajax' );
add_action( 'wp_ajax_nopriv_get_acf_group', 'get_acf_group_ajax' );

function redpoint_scripts() {
  wp_enqueue_script( 'redpoint-api-handler-script', get_template_directory_uri() . '/src/js/utils/redpoint-api-handler.js', array(), '1.0.0', true );
  wp_localize_script( 'redpoint-api-handler-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')) );
}
add_action('wp_enqueue_scripts', 'redpoint_scripts');