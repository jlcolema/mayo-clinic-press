<?php
  /**
   * Series List template
   *
   * @param   array $block The block settings and attributes.
   * @param   string $content The block inner HTML (empty).
   * @param   bool $is_preview True during AJAX preview.
   * @param   (int|string) $post_id The post ID this block is saved to.
   */

  $featured_series = get_field( 'podcast_series' );

  // Arrange taxonomy query
  if ($featured_series) {
    $tax_query = array(
      'relation' => 'AND',
      array(
        'taxonomy' => 'content-formats',
        'field' => 'slug',
        'terms' => 'podcast'
      )
    );

    $arr = array(
      'taxonomy' => $featured_series->taxonomy,
      'field'    => 'term_id',
      'terms'    => $featured_series->term_id
    );

    $tax_query[] = $arr;
  }

  // Set up post query
  $args = array (
    'post_type'             => 'post',
    'tax_query'             => $tax_query,
    'meta_query'            => array(
      'relation'            => 'AND',
      'season_clause'       => array(
        'key'               => 'series_season',
        'type'              => 'NUMERIC',
        'compare'           => 'EXISTS',
      ),
      'episode_clause'      => array(
        'key'               => 'series_episode',
        'type'              => 'NUMERIC',
        'compare'           => 'EXISTS',
      ),
    ),
    'orderby'               => array(
      'season_clause'       => 'ASC',
      'episode_clause'      => 'ASC',
    ),
    'posts_per_page'        => -1
  );

  $the_query = new WP_Query( $args );

  $args['post_status'] = 'future';

  $future_query = new WP_Query( $args );

  if ( $the_query->have_posts() ) : ?>
    <div class="podcast-series">
      <div class="podcast-series__seasons--container">
        <ul class="podcast-series__seasons"></ul>
      </div>

      <ul class="podcast-series__episodes podcast-series__truncated">
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
          <?php get_template_part( 'template-parts/loops/hub', 'podcast-cards' ); ?>
        <?php endwhile; ?>
        <?php while ( $future_query->have_posts() ) : $future_query->the_post(); ?>
          <?php get_template_part( 'template-parts/loops/hub', 'podcast-cards' ); ?>
        <?php endwhile; ?>
        <li class="podcast-series__show-more has-white-color"><a href="javascript:void(0);" class="btn">View All Podcasts</a></li>
      </ul>

      <?php wp_reset_postdata(); ?>
    </div>
  <?php endif; ?>