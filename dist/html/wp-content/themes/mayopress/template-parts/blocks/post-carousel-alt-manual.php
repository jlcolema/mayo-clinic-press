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
$subcopy = get_field('subcopy');
$link = get_field('link');
$img_size = get_field('image_size');
$selected_posts = get_field('hub_selected_posts');
$user_selected_posts = [];
$is_personalized = strpos($className, 'personalized') !== false;

if (!empty($selected_posts)) {
  if ($is_personalized) {
    $first_post = $selected_posts[0];
    $selected_post_type = get_post_type($first_post);

    if ($selected_post_type == 'product') {
      $cachedProducts = WC() && WC()->session ? WC()->session->get('user_algolia_products') : null;
      if ($cachedProducts && count($cachedProducts) >= count($selected_posts)) {
        $user_selected_posts = $cachedProducts;
        $user_selected_posts = array_slice($cachedProducts, 0, count($selected_posts));
      } else {
        $user_selected_posts = get_featured_products(count($selected_posts));
      }
    } elseif ($selected_post_type == 'post') {
      $cachedPosts = WC() && WC()->session ? WC()->session->get('user_algolia_posts') : null;
      if ($cachedPosts && count($cachedPosts) >= count($selected_posts)) {
        $user_selected_posts = $cachedPosts;
        $user_selected_posts = array_slice($cachedPosts, 0, count($selected_posts));
      } else {
        $user_selected_posts = get_featured_posts(count($selected_posts));
      }
    }
  }

  $is_personalized = $is_personalized && !empty($user_selected_posts);
  
  if ($is_personalized) {
    if ($selected_post_type == 'product') {
      $subcopy = $subcopy . "<br><br>" . __('Discover books based on your own interests.', 'mayo');
    }

    foreach ($user_selected_posts as $post) {
      $post->is_personalized_post = true;
    }
  }
  
  // Combine user's personalized products with block's selected products without duplicates
  $all_posts = array_merge($user_selected_posts, $selected_posts);
  $all_posts = array_unique($all_posts, SORT_REGULAR);

  $display_posts = array_slice($all_posts, 0, count($selected_posts));
}
?>

<style>
  #personalized--tooltip-<?php echo $block['id']; ?> {
    display: none;
  }
  label:has(#personalized--tooltip-<?php echo $block['id']; ?>:checked) + .personalized--tooltip {
    display: block;
  }
</style>

<div id="<?php echo esc_attr($id); ?>" class="post-carousel-alt <?php echo esc_attr($className); ?>">
  <?php if ($title): ?>
    <header class="post-carousel-alt__header">
      <h2 class="post-carousel-alt__title heading--lg">
        <?php echo $title; ?>

        <?php if ($is_personalized) : ?>
          <span class="tooltip-icon">
            <label for="personalized--tooltip-<?php echo $block['id']; ?>">
              <svg class="icon icon--info" aria-hidden="true" focusable="false"><use xlink:href="#info"></use></svg>
              <input id="personalized--tooltip-<?php echo $block['id']; ?>" type="checkbox">
            </label>

            <div class="personalized--tooltip personalized--tooltip-<?php echo $block['id']; ?>">
              <p>Recommendations based on your selected topics. You can update them at anytime from My Account.</p>
              <a href="/my-account/my-topics/" class="btn">Manage Topics</a>
              <button class="reset tooltip--close">
                <?php echo __('Close', 'mayo'); ?>
              </button>
            </div>
          </span>
        <?php endif; ?>
      </h2>

      <?php if ( !empty( $subcopy ) ) : ?>
        <p><?php echo $subcopy; ?></p>
      <?php endif; ?>

      <?php if ($link): ?>
        <span class="post-carousel-alt__link">
          <a href="<?php echo $link['url']; ?>" class="btn btn--primary">
            <?php echo $link['title']; ?>
          </a>
        </span>
      <?php endif; ?>
    </header>
  <?php endif; ?>

  <?php if ( $selected_posts ) : ?>
    <div class="slider" data-component="post-carousel-alt">
      <div class="slider__track">
      <?php foreach ( $display_posts as $post ) :
        // Post variables
        $post_id = $post->ID ? $post->ID : $post->get_id();
        $post_type = get_post_type( $post_id );
        $badge_objects = get_the_terms( $post_id, 'content-formats' );
        $post_tags = array();
        $video_post = false;
        $podcast_post = false;
        $excerpt_post = false;
        $runtime = get_field('runtime', $post_id);
        $is_personalized_product = isset($post->is_personalized_post) && $post->is_personalized_post;

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
      ?>
        <div class="slider__item post-card post-card--<?php echo $post_type; ?>">
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
              <?php elseif ($post_type == 'product'): ?>
                <p class="personalized__badge<?php if ( $is_personalized_product ) : ?> personalized-post<?php endif; ?>" <?php if ( !$is_personalized_product ) : ?>aria-hidden="true"<?php endif; ?>><?php echo __('Recommended for you', 'mayo'); ?></p>
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

                <span class="post-card__price">
                  <?php echo wc_price($price); ?>
                </span>
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
  <?php endif; ?>
</div>

<?php if ($is_personalized): ?>
  <script>
      document.addEventListener("DOMContentLoaded", function() {
        document.querySelector('body').addEventListener('click', e => {
          const target = e.target;
          if (e.target.closest('.tooltip--close') || !e.target.closest('.personalized--tooltip-<?php echo $block['id']; ?>')) {
            if (e.target.closest('label[for="personalized--tooltip-<?php echo $block['id']; ?>"]')) return;
            document.querySelector('#personalized--tooltip-<?php echo $block['id']; ?>').checked = false;
          }
        });
      });
  </script>
<?php endif; ?>
