<?php
/**
 * Template Name: URLs Template
 *
 * @package WordPress
 * @subpackage Mayo Clinic Press
 * @since 1.0
 */

$context              = Timber::context();
$context['page']      = new Timber\Post();

Timber::render('templates/header.twig', $context); ?>

<main class="main main--page" id="main">
  <article id="page-{{ page.id }}" class="singular">
    <header class="singular__header">
      <?php if ( function_exists('yoast_breadcrumb') ) {
        yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
      }; ?>
    </header>

    <div class="singular__content">
      <?php
      if ( post_password_required() ) {
        echo get_the_password_form(); // WPCS: XSS ok.
        return;
      }

      function allUrls() {
        $args = array(
          'post_type'      => 'any',
          'posts_per_page' => -1,
        );
        
        $posts = get_posts( $args );
        $data = array();
        
        foreach ( $posts as $post ) {
          $data[] = array(
            'url' => get_permalink( $post->ID ),
            'title' => get_the_title( $post->ID ),
          );
        }
        
        $fp = fopen(wp_upload_dir()['basedir'] . '/all-urls.csv', 'w');
        fputcsv($fp, array('URL', 'Title')); // for the headers
        
        foreach ($data as $fields) {
          fputcsv($fp, $fields);
        }
        
        fclose($fp);
        echo '<p>all-urls.csv saved successfully!</p>';
      }

      function allProductUrls() {
        $args = array(
          'post_type'      => 'product',
          'posts_per_page' => -1,
        );
        
        $products = get_posts( $args );
        $data = array();
        
        foreach ( $products as $product ) {
          $categories = array();
          $subcategories = array();
          
          $terms = get_the_terms( $product->ID, 'product_cat' );
          
          foreach ( $terms as $term ) {
            if ($term->parent == 0) {
              $categories[] = $term->name;
            } else {
              $subcategories[] = $term->name;
            }
          }
          
          $data[] = array(
            'url' => get_permalink( $product->ID ),
            'title' => get_the_title( $product->ID ),
            'categories' => implode(', ', $categories),
            'subcategories' => implode(', ', $subcategories),
          );
        }
        
  $fp = fopen(wp_upload_dir()['basedir'] . '/product-urls.csv', 'w');
        fputcsv($fp, array('URL', 'Title', 'Categories', 'Subcategories')); // for the headers
        
        foreach ($data as $fields) {
          fputcsv($fp, $fields);
        }
        
        fclose($fp);
        echo '<p>product-urls.csv saved successfully!</p>';
      }

      function categoryUrls($categories) {
        foreach ($categories as $category_slug) {
          $args = array(
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'category_name'  => $category_slug,
          );
      
          $posts = get_posts( $args );
          if (empty($posts)) {
            echo "<p>No posts found for category: $category_slug</p>";
            continue; // skip to the next category
          }
      
          $data = array();
      
          foreach ( $posts as $post ) {
            $data[] = array(
              'url' => get_permalink( $post->ID ),
              'title' => get_the_title( $post->ID ),
            );
          }
      
          $fp = fopen(wp_upload_dir()['basedir'] . '/' . $category_slug . '-urls.csv', 'w');
          fputcsv($fp, array('URL', 'Title')); // for the headers
      
          foreach ($data as $fields) {
            fputcsv($fp, $fields);
          }
      
          fclose($fp);
          echo "<p>" . $category_slug . "-urls.csv saved successfully!</p>";
        }
      }

      allUrls();
      allProductUrls();
      categoryUrls(array('healthy-aging', 'healthy-brain', 'healthy-heart', 'diabetes'));

      ?>
    </div>
  </article>
</main>

<?php
Timber::render('templates/footer.twig', $context);
