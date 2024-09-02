<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Shop Name to catalog section.
 */
add_action( 'customize_register', 'set_woommerce_shop_name' );

function set_woommerce_shop_name( $wp_customize ) {
   $wp_customize->add_setting( 'woocommerce_shop_name', array(
      'default'           => '',
      'type'              => 'option',
      'capability'        => 'manage_woocommerce',
      'sanitize_callback' => 'wp_filter_nohtml_kses',
   ));
   $wp_customize->add_control( 'woocommerce_shop_name', array(
      'label'             => 'Change shop name',
      'section'           => 'woocommerce_product_catalog',
      'settings'          => 'woocommerce_shop_name',
      'type'              => 'text',
      'priority'          => 1,
   ));
}

/**
 * Add banner to catalog section.
 */
add_action( 'customize_register', 'add_plp_banner' );
 
function add_plp_banner( $wp_customize ) {
   $wp_customize->add_setting( 'woocommerce_plp_banner_text', array(
      'default'           => '',
      'type'              => 'option',
      'capability'        => 'manage_woocommerce',
      'sanitize_callback' => 'wp_filter_nohtml_kses',
   ));
   $wp_customize->add_control( 'woocommerce_plp_banner_text', array(
      'label'             => 'Additional Banner',
      'description'       => 'Additional text and link that will appear between the 2nd and 3rd row of the Product Listings page.',
      'section'           => 'woocommerce_product_catalog',
      'settings'          => 'woocommerce_plp_banner_text',
      'type'              => 'textarea',
   ));
   $wp_customize->add_setting( 'woocommerce_plp_banner_button', array(
      'default'           => '',
      'type'              => 'option',
      'capability'        => 'manage_woocommerce',
      'sanitize_callback' => 'wp_filter_nohtml_kses',
   ));
   $wp_customize->add_control( 'woocommerce_plp_banner_button', array(
      'label'             => 'Additional Banner Link',
      'description'       => 'Additional link text will read:',
      'section'           => 'woocommerce_product_catalog',
      'settings'          => 'woocommerce_plp_banner_button',
      'type'              => 'text',
   ));
   $wp_customize->add_setting( 'woocommerce_plp_banner_url', array(
      'default'           => '',
      'type'              => 'option',
      'capability'        => 'manage_woocommerce',
      'sanitize_callback' => 'esc_url_raw',
   ));
   $wp_customize->add_control( 'woocommerce_plp_banner_url', array(
      'label'             => 'Additional Banner Link URL',
      'section'           => 'woocommerce_product_catalog',
      'settings'          => 'woocommerce_plp_banner_url',
      'type'              => 'url',
   ));
   $wp_customize->add_setting( 'woocommerce_plp_banner_color_scheme', array(
      'default'           => 'accent',
      'type'              => 'option',
      'capability'        => 'manage_woocommerce',
   ));
   $wp_customize->add_control( 'woocommerce_plp_banner_color_scheme', array(
      'label'             => 'Banner Background Color',
      'section'           => 'woocommerce_product_catalog',
      'settings'          => 'woocommerce_plp_banner_color_scheme',
      'type'              => 'select',
      'choices'           => array(
         'white'          => 'White background',
         'black'          => 'Black background',
         'primary'        => 'Blue background',
         'primary-light'  => 'Bright blue background',
         'accent'         => 'Teal background',
         'quaternary'     => 'Green background',
         'secondary'      => 'Purple background',
         'quinary'        => 'Red background',
         'tertiary'       => 'Orange background',
         'senary'         => 'Yellow background',
      ),
   ));
}

/**
 * Add logo to footer
 */
add_action( 'customize_register', 'add_footer_logo' );

function add_footer_logo( $wp_customize ) {
   $wp_customize->add_setting( 'footer_logo', array(
      'default'           => '',
      'sanitize_callback' => 'absint',
   ));
   $wp_customize->add_control(
      new WP_Customize_Media_Control(
         $wp_customize,
         'footer_logo',
         array(
            'label' => 'Upload Footer Logo',
            'section' => 'title_tagline',
            'settings' => 'footer_logo',
            'button_labels' => array(
               'select' => 'Select Logo',
               'remove' => 'Remove',
               'change' => 'Change Logo',
            )
         )
      )
   );
}
