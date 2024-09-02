<?php

$announcement_panel = acf_add_customizer_panel(array(
  'title' => 'Announcement Bar',
));

acf_add_customizer_section(array(
  'title'        => 'Announcement Bar Text',
  'storage_type' => 'option',
  'panel'        => $announcement_panel
));

acf_add_customizer_section(array(
  'title'        => 'Free Shipping Banner',
  'storage_type' => 'option',
  'panel'        => $announcement_panel
));

$footer_panel = acf_add_customizer_panel(array(
  'title' => 'Footer Options',
));

acf_add_customizer_section(array(
  'title'        => 'Social Sharing Links',
  'storage_type' => 'option',
  'panel'        => $footer_panel
));
