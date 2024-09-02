<?php
/**
 * Custom page template for privacy-policy
 * 
 * Grabs page content from the mayoclinic.org privacy policy
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context              = Timber::context();
$context['page']      = new Timber\Post();

// get content from url
$html = file_get_contents( 'https://www.mayoclinic.org/about-this-site/privacy-policy' );

// Replace relative links with absolute links
$html = str_replace('<a href="/', '<a href="https://www.mayoclinic.org/', $html);

// load html into a DOMDocument
$dom = new DOMDocument();
@$dom->loadHTML( $html );

// function to recursively find the node with actual content
function findContentNode($node) {
  if ($node->hasChildNodes() && $node->childNodes->length == 1) {
    return findContentNode($node->firstChild);
  }
  return $node;
}

// get elements with id main-content
$xpath = new DOMXPath( $dom );
$result = $xpath->query( '//*[@id="main-content"]' );

// get innerHTML of main-content
$context['privacy_policy'] = "";
if ($result->length > 0) {
  $mainContent = findContentNode($result->item(0));
  foreach ($mainContent->childNodes as $node) {
    if ( strpos($node->ownerDocument->saveHTML( $node ), 'data-testid="cmp-text"' ) !== false ) {
      $context['privacy_policy'] .= $dom->saveHTML($node);
      break;
    }
  }
}

Timber::render('templates/page-privacy-policy.twig', $context);
