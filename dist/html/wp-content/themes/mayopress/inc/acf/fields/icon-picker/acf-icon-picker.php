<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('AcfIconPickerField')) {
  class AcfIconPickerField {
    function __construct() {
      $this->settings = array(
        'version'	=> '1.0.0',
        'url'		  => get_template_directory_uri(),
        'path'		=> get_template_directory()
      );

      add_action('acf/include_field_types', array($this, 'include_field_types'));
    }

    function include_field_types($version = false) {
      include_once('fields/acf-icon-picker-v5.php');
    }
  }

  new AcfIconPickerField();
}
