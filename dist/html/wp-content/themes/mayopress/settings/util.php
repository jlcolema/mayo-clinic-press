<?php

/**
 * Create a shortcode for the current year
 *
 * @return string Date string of the current year
 */
function year_shortcode() {
  return date('Y');
}

add_shortcode('year', 'year_shortcode');

/**
 * Get the contents of an SVG icon from the file name
 *
 * @param string $filename
 *
 * @return string|undefined The SVG file contents, or nothing
 */
function get_the_svg($filename) {
  $path = MAYO_THEME_ICONS . $filename . '.svg';
  if (file_exists($path)) {
    return file_get_contents($path);
  }
}
