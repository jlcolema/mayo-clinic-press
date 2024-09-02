<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('AcfImageSizeSelectField')) {
  class AcfImageSizeSelectField {
    function __construct() {
      $this->settings = array(
        'version'	=> '1.0.0',
        'url'		  => get_template_directory_uri(),
        'path'		=> get_template_directory()
      );

      add_action('acf/include_field_types', array($this, 'include_field_types'));
    }

    function include_field_types($version = false) {
      include_once('fields/acf-image-size-select-v5.php');
    }
  }

  new AcfImageSizeSelectField();
}
