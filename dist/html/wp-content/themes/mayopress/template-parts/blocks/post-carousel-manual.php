<?php

/**
 * Post carousel manual template
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value
$id = 'post-carousel-' . $block['id'];
if (!empty($block['anchor'])) {
  $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values
$className = 'post-carousel';
if (!empty($block['className'])) {
  $className .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
  $className .= ' align' . $block['align'];
}

// Get field values
$title = get_field('title');
$link = get_field('link');
$img_size = get_field('image_size');
$selected_posts = get_field('hub_selected_posts');

if (get_field('add_icon')) {
  $icon = get_field('icon');
  $icon_color = get_field('icon_color');
}

$add_filter = get_field('add_filters');
if ( $add_filter ) {
  $taxonomy = 'product_cat';
  $selected_parent_category = get_field('filter');

  $taxonomy_terms = array();
  if ($selected_posts && $selected_parent_category) {
    $terms_array = array();
    foreach ($selected_posts as $post) {
      $terms = get_the_terms($post->ID, $taxonomy);
      if ($terms && ! is_wp_error($terms)) {
        foreach ($terms as $term) {
          // Check if this term's parent is the selected parent category
          if ($term->parent == $selected_parent_category) {
            $terms_array[$term->term_id] = $term->name;
          }
        }
      }
    }
    // Sort terms by value (term name)
    asort($terms_array);
    // Save names to $taxonomy_terms
    $taxonomy_terms = array_values($terms_array);
  }
}
?>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
  <?php if ($title): ?>
    <header class="post-carousel__header">
      <h2 class="post-carousel__title heading--lg">
        <?php if ($icon): ?>
          <i class="icon icon--<?php echo $icon; ?>"<?php if ($icon_color) : ?> style="color: var(--wp--preset--color--<?php echo $icon_color; ?>);"<?php endif; ?>>
            <?php echo get_the_svg($icon); ?>
          </i>
        <?php endif; ?>
        <?php echo $title; ?>
      </h2>

      <?php if ($add_filter) : ?>
        <div class="post-carousel__filters">
          <?php foreach($taxonomy_terms as $term) : ?>
            <button type="button" class="btn btn--filter"><?php echo $term; ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($link): ?>
        <span class="post-carousel__link text--md">
          <a href="<?php echo $link['url']; ?>">
            <?php echo $link['title']; ?>

            <svg class="icon icon--arrow-right" aria-hidden="true" focusable="false">
              <use xlink:href="#arrow-right"></use>
            </svg>
          </a>
        </span>
      <?php endif; ?>
    </header>
  <?php endif; ?>

  <div class="slider" data-snap-slider>
    <div class="slider__track">
    <?php foreach ( $selected_posts as $post ) :
      // Post variables
      $post_id = $post->ID;
      $post_type = get_post_type( $post_id );
      $badge_objects = get_the_terms( $post_id, 'content-formats' );
      $post_tags = array();
      $video_post = false;
      $podcast_post = false;
      $runtime = get_field('runtime', $post_id);

      // Set video, podcast, or book excerpt post
      if ($badge_objects) {
        foreach ($badge_objects as $tag) {
          $post_tags[] = $tag->slug;

          if ($tag->slug === 'video') {
            $video_post = true;
          }

          if ($tag->slug === 'podcast') {
            $podcast_post = true;
          }

          $format_name = $tag->name;
        }
      }

      if ($taxonomy) {
        // Get the terms for this post
        $terms = get_the_terms($post_id, $taxonomy);
        $term_ids = array();
        if ($terms && !is_wp_error($terms)) {
          foreach ($terms as $term) {
            // Only include this term if its parent is the selected parent category
            if ($term->parent == $selected_parent_category) {
              $term_ids[] = $term->term_id;
            }
          }
        }
      
        // Create the data-terms attribute value
        $term_names = array_map(function($term_id) use ($terms_array) {
          return $terms_array[$term_id] ?? '';
        }, $term_ids);
        
        $data_terms = implode(',', array_filter($term_names));
      }
    ?>
      <div class="slider__item post-card post-card--<?php echo $post_type; ?>"<?php if ($taxonomy) : ?> data-terms="<?php echo esc_attr($data_terms); ?>"<?php endif; ?>>
        <a href="<?php echo get_the_permalink( $post_id ); ?>">
          <?php if (has_post_thumbnail( $post_id )): ?>
            <div class="post-card__image">
              <?php echo get_the_post_thumbnail( $post_id, $img_size ); ?>
            </div>
          <?php endif; ?>

          <div class="post-card__content">
            <?php if ($post_type === 'post'): ?>
              <span class="post-card__type heading--xs">
                <?php echo $format_name; ?>                  
              </span>
            <?php endif; ?>

            <div class="post-card__title heading--sm">
              <?php echo get_the_title( $post_id ); ?>
            </div>

            <?php if ($post_type === 'post'): ?>
              <div class="post-card__meta text--sm">
                <time class="post-card__date">
                  <?php echo get_the_time( 'F jS, Y', $post_id ); ?>
                </time>
                <?php if (($video_post || $podcast_post) && $runtime): ?>
                  <span class="post-card__runtime">
                    <svg class="icon icon--clock" aria-hidden="true" focusable="false">
                      <use xlink:href="#clock"></use>
                    </svg>
                    <span class="sr-only"><?php echo __('Runtime:', 'mayo'); ?></span>
                      <?php
                        // Build runtime
                        $formatted_time = '';

                        if ( $runtime['hours'] ) {
                          $formatted_time .= $runtime['hours'] . ':';
                        }

                        if ( $runtime['minutes'] ) {
                          if ( substr( $formatted_time, -1 ) == ':' && $runtime['minutes'] < 10 ) {
                            $formatted_time .= '0';
                          }
                          if ( $runtime['minutes'] == 0 ) {
                            $formatted_time .= '0:';
                          } else {
                            $formatted_time .= $runtime['minutes'] . ':';
                          }
                        } elseif ( substr( $formatted_time, -1 ) == ':' ) {
                          $formatted_time .= '00:';
                        }

                        if ( $runtime['seconds'] ) {
                          if ( substr( $formatted_time, -1 ) == ':' ) {
                            if ( $runtime['seconds'] < 10 ) {
                              $formatted_time .= '0';
                            }
                            if ( $runtime['seconds'] == 0 ) {
                              $formatted_time .= '0';
                            } else {
                              $formatted_time .= $runtime['seconds'];
                            }
                          } else {
                            $formatted_time .= $runtime['seconds'] . ' seconds';
                          }
                        } elseif ( substr( $formatted_time, -1 ) == ':' ) {
                          $formatted_time .= '00';
                        }

                        echo $formatted_time;
                      ?>
                  </span>
                <?php elseif (!$video_post && !$podcast_post): ?>
                <?php
                  $authors = get_field( 'post_authors', $post_id );
                  if ( $authors ) {
                    echo '<span class="post-card__author">';
                    echo '<span class="sr-only">Written by: </span>';
                    echo implode( ', <br class="is-hidden--desktop">', $authors );
                    echo '</span>';
                  }
                ?>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php if ($post_type === 'product'):
              $price = get_post_meta($post_id, '_price', true);
            ?>
              <div class="post-card__price">
                <?php echo wc_price($price); ?>
              </div>
            <?php endif; ?>

            <div class="btn <?php echo $post_type === 'product' ? 'btn--primary' : ''; ?> post-card__btn">
              <?php
                  if ($post_type === 'post') {
                    if ($video_post) _e('Watch', 'mayo');
                    elseif ($podcast_post) _e('Listen', 'mayo');
                    else _e('Learn More', 'mayo');
                  } elseif ($post_type === 'product') {
                    _e('View Details', 'mayo');
                  }
              ?>
              <span class="sr-only">
                &ndash; <?php echo get_the_title( $post_id ); ?>
              </span>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
    </div>

    <button class="slider__arrow slider__arrow--prev">
      <span class="sr-only">
        <?php _e('Previous slide', 'mayo'); ?>
      </span>
      <svg class="icon icon--chevron-left" aria-hidden="true" focusable="false">
        <use xlink:href="#chevron-left"></use>
      </svg>
    </button>
    <button class="slider__arrow slider__arrow--next">
      <span class="sr-only">
        <?php _e('Next slide', 'mayo'); ?>
      </span>
      <svg class="icon icon--chevron-right" aria-hidden="true" focusable="false">
        <use xlink:href="#chevron-right"></use>
      </svg>
    </button>
    <div class="slider__pagination"></div>
  </div>
</div>
