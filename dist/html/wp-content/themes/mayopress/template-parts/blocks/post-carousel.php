<?php

/**
 * Post carousel template
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
$post_type = get_field('post_type');
$post_terms = get_field('post_terms');
$term_relation = get_field('term_relation');
$sort_by = get_field('sort_by');
$limit = get_field('number_of_items');
$tax_query = array('relation' => 'OR');
$order_by = 'date';
$order = 'DESC';
$img_size = get_field('image_size');
$page_id = get_the_ID();
$featured_slider_post_ids = get_featured_slider_post_ids($page_id);
$user_selected_posts = [];
$is_personalized = strpos($className, 'personalized') !== false;
$the_query = [];

if ($term_relation) {
  $tax_query = array('relation' => 'AND');
}

// Arrange taxonomy query
if ($post_terms) {
  foreach ($post_terms as $term) {
    $arr = array(
      'taxonomy' => $term->taxonomy,
      'field'    => 'term_id',
      'terms'    => $term->term_id
    );

    $tax_query[] = $arr;
  }
}

// Arrange sorting options
if ($sort_by) {
  $sort_by = explode('-', $sort_by);
  $order_by = $sort_by[0];
  $order = $sort_by[1];
}

// Set up post query
$args = array (
  'post_type'      => $post_type,
  'tax_query'      => $tax_query,
  'orderby'        => $order_by,
  'order'          => $order,
  'posts_per_page' => $limit,
  'post__not_in'   => $featured_slider_post_ids
);

if ($is_personalized) {
  if ($post_type == 'product') {
    $cachedProducts = WC() && WC()->session ? WC()->session->get('user_algolia_products') : null;
    if ($cachedProducts && count($cachedProducts) >= $limit) {
      $user_selected_posts = $cachedProducts;
      $user_selected_posts = array_slice($cachedProducts, 0, $limit);
    } else {
      $user_selected_posts = get_featured_products($limit);
    }
  } elseif ($post_type == 'post') {
    $cachedPosts = WC() && WC()->session ? WC()->session->get('user_algolia_posts') : null;
    if ($cachedPosts && count($cachedPosts) >= $limit + 3) {
      $user_selected_posts = $cachedPosts;
      $user_selected_posts = array_slice($cachedPosts, 3, $limit);
    } else {
      $user_selected_posts = get_featured_posts($limit + 3);
      $user_selected_posts = array_slice($user_selected_posts, 3);
    }
  }
}

$is_personalized = $is_personalized && !empty($user_selected_posts);

if ($is_personalized) {
  $title = "Based on Your Interests";
  $the_query = $user_selected_posts;
} elseif (strpos($className, 'personalized') === false) {
  $the_query = new WP_Query($args);
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

<?php if ($the_query instanceof WP_Query ? $the_query->have_posts() : !empty($the_query)) : ?>
  <div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
    <?php if ($title): ?>
      <header class="post-carousel__header">
        <h2 class="post-carousel__title heading--lg">
          <?php echo $title; ?>
        </h2>

        <?php if ($is_personalized) : ?>
          <span class="tooltip-icon">
            <label for="personalized--tooltip-<?php echo $block['id']; ?>">
              <svg class="icon icon--info" aria-hidden="true" focusable="false"><use xlink:href="#info"></use></svg>
              <input id="personalized--tooltip-<?php echo $block['id']; ?>" type="checkbox">
            </label>

            <div class="personalized--tooltip personalized--tooltip-<?php echo $block['id']; ?>">
              <p>You can update your selected topics at anytime from My Account.</p>
              <a href="/my-account/my-topics/" class="btn">Manage Topics</a>
              <button class="reset tooltip--close">
                <?php echo __('Close', 'mayo'); ?>
              </button>
            </div>
          </span>
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
        <?php if ($the_query instanceof WP_Query) :
          while ($the_query->have_posts()) : 
            $the_query->the_post();

            // Post variables
            $post_id = get_the_ID();
            $post_type = get_post_type();
            $badge_objects = get_the_terms($post_id, 'content-formats');
            $post_tags = array();
            $video_post = false;
            $podcast_post = false;
            $runtime = get_field('runtime', $post_id);

            // Set video or podcast post
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
              <a href="<?php the_permalink(); ?>">
                <?php if (has_post_thumbnail()): ?>
                  <div class="post-card__image">
                    <?php the_post_thumbnail($img_size); ?>
                  </div>
                <?php endif; ?>

                <div class="post-card__content">
                  <?php if ($post_type === 'post'): ?>
                    <span class="post-card__type heading--xs">
                      <?php echo $format_name; ?>                  
                    </span>
                  <?php endif; ?>

                  <div class="post-card__title heading--sm">
                    <?php the_title(); ?>
                  </div>

                  <?php if ($post_type === 'post'): ?>
                    <div class="post-card__meta text--sm">
                      <time class="post-card__date">
                        <?php the_time('F jS, Y'); ?>
                      </time>

                      <?php if (($video_post || $podcast_post) && $runtime): ?>
                        <span class="post-card__runtime">
                          <svg class="icon icon--clock" aria-hidden="true" focusable="false">
                            <use xlink:href="#clock"></use>
                          </svg>
                          <span class="sr-only"><?php echo __('Runtime:', 'mayo'); ?></span>
                          <?php echo format_runtime($runtime); ?>
                        </span>
                      <?php elseif (!$video_post && !$podcast_post): ?>
                        <?php
                          $authors = get_field('post_authors', $post_id);
                          if ($authors) {
                            echo '<span class="post-card__author">';
                            echo '<span class="sr-only">Written by: </span>';
                            echo implode(', <br class="is-hidden--desktop">', $authors);
                            echo '</span>';
                          }
                        ?>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>

                  <?php if ($post_type === 'product') :
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
                        &ndash; <?php the_title(); ?>
                      </span>
                  </div>
                </div>
              </a>
            </div>
          <?php endwhile; ?>
        <?php elseif (is_array($the_query)) : ?>
          <?php foreach ($the_query as $post) : 
            // Post variables
            $post_id = $post->ID;
            $post_type = get_post_type($post_id);
            $badge_objects = get_the_terms($post_id, 'content-formats');
            $post_tags = array();
            $video_post = false;
            $podcast_post = false;
            $runtime = get_field('runtime', $post_id);

            // Set video or podcast post
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
              <a href="<?php the_permalink($post_id); ?>">
                <?php if (has_post_thumbnail($post_id)): ?>
                  <div class="post-card__image">
                    <?php echo get_the_post_thumbnail($post_id, $img_size); ?>
                  </div>
                <?php endif; ?>

                <div class="post-card__content">
                  <?php if ($post_type === 'post'): ?>
                    <span class="post-card__type heading--xs">
                      <?php echo $format_name; ?>                  
                    </span>
                  <?php endif; ?>

                  <div class="post-card__title heading--sm">
                    <?php echo get_the_title($post_id); ?>
                  </div>

                  <?php if ($post_type === 'post'): ?>
                    <div class="post-card__meta text--sm">
                      <time class="post-card__date">
                        <?php echo get_the_date('F jS, Y', $post_id); ?>
                      </time>

                      <?php if (($video_post || $podcast_post) && $runtime): ?>
                        <span class="post-card__runtime">
                          <svg class="icon icon--clock" aria-hidden="true" focusable="false">
                            <use xlink:href="#clock"></use>
                          </svg>
                          <span class="sr-only"><?php echo __('Runtime:', 'mayo'); ?></span>
                          <?php echo format_runtime($runtime); ?>
                        </span>
                      <?php elseif (!$video_post && !$podcast_post): ?>
                        <?php
                          $authors = get_field('post_authors', $post_id);
                          if ($authors) {
                            echo '<span class="post-card__author">';
                            echo '<span class="sr-only">Written by: </span>';
                            echo implode(', <br class="is-hidden--desktop">', $authors);
                            echo '</span>';
                          }
                        ?>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>

                  <?php if ($post_type === 'product') :
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
                        &ndash; <?php get_the_title($post_id); ?>
                      </span>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
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

    <?php wp_reset_postdata(); ?>
  </div>

  <?php if ($is_personalized): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        var latestCarousel = document.querySelector('.redpoint_latest_carousel');
        var productCarousel = document.querySelector('.redpoint_product_carousel');

        if (latestCarousel && productCarousel) {
          productCarousel.after(latestCarousel);
        }

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
<?php endif; ?>