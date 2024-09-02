<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function check_privacy_update() {
  if (!is_admin()) {
    // get content from url
    $html = file_get_contents( 'https://www.mayoclinic.org/about-this-site/privacy-policy' );

    // load html into a DOMDocument
    $dom = new DOMDocument();
    @$dom->loadHTML( $html );

    // function to recursively find the node with the "Updated on" text
    function findUpdatedOnNode($node) {
      if ($node instanceof DOMElement && strpos($node->textContent, 'Updated on') !== false) {
        foreach ($node->childNodes as $child) {
          $foundNode = findUpdatedOnNode($child);
          if ($foundNode !== null) {
            return $foundNode;
          }
        }
        return $node;
      }
      return null;
    }

    // get elements with id main-content
    $xpath = new DOMXPath( $dom );
    $result = $xpath->query( '//*[@id="main-content"]' );

    // get innerHTML of main-content
    if ($result->length > 0) {
      $mainContent = $result->item(0);
      $updatedOnNode = findUpdatedOnNode($mainContent);

      if ($updatedOnNode !== null) {
        $privacy_policy = wp_strip_all_tags($dom->saveHTML($updatedOnNode));
        $privacy_policy = str_replace( 'Updated on', '', $privacy_policy );
        $dateString = trim( $privacy_policy );

        // convert the date string into a timestamp
        $dateObject = strtotime( $dateString );
        if ($dateObject) {
          echo '<script>var timestamp = ' . $dateObject . ';</script>';
        }
      }
    }
  }
}
add_action('wp_footer', 'check_privacy_update');