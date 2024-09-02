<?php
/**
 * Template Name: Posts JSON Template
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

      // create posts json file
      $args = array( 
        'post_type' => 'post',
        'nopaging' => true,
        'posts_per_page' => 7
      );
      $query = new WP_Query( $args ); // $query is the WP_Query Object
      $posts = $query->get_posts();   // $posts contains the post objects

      $output = array();
      foreach( $posts as $post ) {
        $output['posts'][] = array(
          'id' => $post->ID,
          'post_type' => $post->post_type,
          'title' => $post->post_title,
          'name' => $post->post_name,
          'authors_contributors' => get_field( 'authors_contributors', $post->ID ),
          'article_authors' => get_field( 'post_authors', $post->ID ),
          'creator' => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post->ID ) ),
          'date' => $post->post_date,
          'categories' => get_the_category( $post->ID ),
          'tags' => get_the_tags( $post->ID ),
          'post_format' => get_the_terms( $post->ID, 'content-formats' ),
          'featured_image' => get_the_post_thumbnail_url( $post->ID ),
          'embedded_video_or_podcast' => get_field( 'embed_code', $post->ID ),
          'excerpt' => $post->post_excerpt,
          'content' => $post->post_content,
          'share_facebook' => get_field( 'share_on_facebook', $post->ID ),
          'share_twitter' => get_field( 'share_on_twitter', $post->ID ),
          'share_linkedin' => get_field( 'share_on_linkedin', $post->ID ),
          'share_email' => get_field( 'share_on_email', $post->ID ),
          'permalink' => get_permalink( $post->ID )
        );
      }

      // save json file in uploads folder
      $data_posts = json_encode( $output );
      $upload_dir = wp_get_upload_dir();
      $file_name = date('Y-m-d') . '-posts.json';
      $save_path = $upload_dir['basedir'] . '/' . $file_name;
      $f = @fopen( $save_path , "w" );
      fwrite($f , $data_posts);
      fclose($f);

      // Download posts json file
      // if ($save_path) {
      //   header('Content-Description: File Transfer');
      //   header('Content-Type: application/octet-stream');
      //   header('Content-Disposition: attachment; filename="'.basename($save_path).'"');
      //   header('Content-Length: ' . filesize($save_path));
      //   header('Content-Transfer-Encoding: binary');
      //   header('Expires: 0');
      //   header('Cache-Control: must-revalidate');
      //   header('Pragma: public');
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
