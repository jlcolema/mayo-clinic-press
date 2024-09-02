<?php
/**
 * Template Name: Products JSON Template
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


      // create products json file
      $args = array( 
        'post_type' => 'product',
        'nopaging' => true,
        'posts_per_page' => 15
      );
      $query = new WP_Query( $args ); // $query is the WP_Query Object
      $posts = $query->get_posts();   // $posts contains the post objects

      $output = array();
      foreach( $posts as $post ) {
        $product = wc_get_product( $post );

        $facts = '';
        if ( get_field( 'show_facts_section', $post->ID ) ) {
          $facts = get_field( 'large_text', $post->ID ) . ' ' . get_field( 'facts_content', $post->ID );
        }

        $output['products'][] = array(
          'id' => $post->ID,
          'post_type' => $post->post_type,
          'product_type' => $product->get_type(),
          'title' => $product->get_name(),
          'name' => $product->get_slug(),
          'date' => $post->post_date,
          'description' => $product->get_description(),
          'short_description' => $product->get_short_description(),
          'sku' => $product->get_sku(),
          'permalink' => get_permalink( $product->get_id() ),
          'price' => $product->get_price(),
          'price_regular' => $product->get_regular_price(),
          'price_sale' => $product->get_sale_price(),
          'total_sales' => $product->get_total_sales(),
          'upsell_ids' => $product->get_upsell_ids(),
          'cross_sell_ids' => $product->get_cross_sell_ids(),
          'variations' => $product->get_children(), // get variations
          'attributes' => $product->get_attributes(),
          'attributes_default' => $product->get_default_attributes(),
          'categories' => get_the_terms( $post->ID, 'product_cat' ),
          'product_format' => get_the_terms( $post->ID, 'product-formats' ),
          'plp_description' => get_field( 'product_listing_description', $post->ID ),
          'relevant_reading_block_description' => get_field( 'relevant_reading_description', $post->ID ),
          'product_authors' => get_field( 'product_authors', $post->ID ),
          'the_facts' => $facts,
          'elfsight_embed_code' => get_field( 'elfsight_embed', $post->ID ),
          'featured_image' => get_the_post_thumbnail_url( $post->ID )
        );
      }

      // save json file in uploads folder
      $data_posts = json_encode( $output );
      $upload_dir = wp_get_upload_dir();
      $file_name = date('Y-m-d') . '-products.json';
      $save_path = $upload_dir['basedir'] . '/' . $file_name;
      $f = @fopen( $save_path , "w" );
      fwrite($f , $data_posts);
      fclose($f);

      // Download products json file
      // if ($save_path) {
      //   header('Content-Description: File Transfer');
      //   header('Content-Type: application/octet-stream');
      //   header('Content-Disposition: attachment; filename="'.basename($save_path).'"');
      //   header('Expires: 0');
      //   header('Cache-Control: must-revalidate');
      //   header('Pragma: public');
      //   header('Content-Length: ' . filesize($save_path));
      //   readfile($save_path);
      //   exit;
      // }
      ?>
      <p><?php echo $file_name; ?> saved successfully!</p>
    </div>
  </article>
</main>

<?php
Timber::render('templates/footer.twig', $context);
