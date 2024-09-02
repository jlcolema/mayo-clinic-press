<?php
  /**
   * CMS Content Template
   *
   * @param   array $block The block settings and attributes.
   * @param   string $content The block inner HTML (empty).
   * @param   bool $is_preview True during AJAX preview.
   * @param   (int|string) $post_id The post ID this block is saved to.
   */

  $api_key = get_field('cms_api_key', 'external_settings');
  $content_id = get_field('cms_content_id');

  if ( ! function_exists( 'get_cms_content' ) ) {
    function get_cms_content($content_id, $apikey) {
      $uri = 'https://cdapi.mayoclinic.org/api/v2/content/' . $content_id . '/en';

      // Set headers
      $headers = array(
        'Accept' => 'application/json',
        'apikey' => $apikey,
        'trckr'  => $apikey
      );

      // Make the request
      $response = wp_remote_get($uri, array('headers' => $headers));

      // Check for errors
      if (is_wp_error($response)) {
        return null;
      }

      // Check for success
      if (200 !== wp_remote_retrieve_response_code($response)) {
        return null;
      }

      // Parse the response
      $data = json_decode(wp_remote_retrieve_body($response), true);

      // Output the content
      if (isset($data['body']['concept'][0])) {
        $concepts = $data['body']['concept'];
        foreach ($concepts as $concept) {
          // add heading for conceptHead
          if ($concept['conceptHead'] !== '') {
            echo '<h2>' . $concept['conceptHead'] . '</h2>';
          }

          $sections = $concept['section'];
          foreach ($sections as $section) {
            // add heading for sectionHead
            if ($section['sectionHead'] !== '') {
              echo '<h3>' . $section['sectionHead'] . '</h3>';
            }

            $elements = $section['Elements'];
            foreach ($elements as $element) {
              if (isset($element['HTML'])) {
                echo '<div>' . $element['HTML'] . '</div>';
              }
              if (isset($element['image'])) {
                $image = $element['image'];
                render_cms_image($image, $apikey);
              }
            }
          }
        }
      
        echo '<div>' . $data['footer'] . '</div>';
      }
    }

    if ( ! function_exists( 'render_cms_image' ) ) {
      function render_cms_image($imageObj, $apikey) {
        $image_uri = 'https://cdapi.mayoclinic.org/api/v2/content/' . $imageObj['id'] . '/en';
  
        // Set headers
        $image_headers = array(
          'Accept' => '*/*',
          'apikey' => $apikey,
          'trckr'  => $apikey
        );
      
        // Make the request
        $image_response = wp_remote_get($image_uri, array('headers' => $image_headers));
        if (!is_wp_error($image_response)) {
          $image_data = wp_remote_retrieve_body($image_response);
          $base64_image = 'data:image/jpeg;base64,' . base64_encode($image_data);
          echo '<img src="' . $base64_image . '" alt="" height="' . $imageObj['height'] . '" width="' . $imageObj['width'] . '">';
        }
      }      
    }

    get_cms_content($content_id, $api_key);
  }