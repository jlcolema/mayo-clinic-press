<?php

/**
 * Featured posts template
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value
$id = 'featured-posts-' . $block['id'];
if (!empty($block['anchor'])) {
  $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values
$className = 'featured-posts';
if (!empty($block['className'])) {
  $className .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
  $className .= ' align' . $block['align'];
}

// Get field values
$title = get_field('title');
$invisible_overlay = !get_field('is_visible');
$post_type = get_field('post_type');
$post_terms = get_field('post_terms');
$sort_by = get_field('sort_by');
$limit = get_field('number_of_items');
$tax_query = array('relation' => 'OR');
$order_by = 'date';
$order = 'DESC';

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
  'posts_per_page' => 3
);

$the_query = new WP_Query($args);

?>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
  <?php if ($title): ?>
  <?php endif; ?>

  <?php if ($the_query->have_posts()): ?>
    <div class="slider" data-snap-slider>
      <div class="slider__track">
      <header class="featured-posts__header">
        <h2 class="featured-posts__title heading--lg"><?php echo $title; ?></h2>
      </header>
      <?php while ($the_query->have_posts()): $the_query->the_post(); ?>
        <div class="slider__item post-card post-card--post">
          <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()): ?>
              <div class="post-card__image">
                <?php the_post_thumbnail('featured-thumbnail'); ?>
            </div>
            <?php endif; ?>

            <div class="post-card__content<?php if ( $invisible_overlay && $the_query->current_post == 0 ) : ?> is-invisible<?php endif; ?>">
              <div class="post-card__title heading--sm">
                <?php the_title(); ?>
              </div>

              <div class="post-card__meta text--sm">
                <time class="post-card__date">
                  <?php the_time('F jS, Y'); ?>
                </time>
                <?php
                  $authors = get_field( 'post_authors', get_the_ID() );
                  if ( $authors ) {
                    echo '<span class="post-card__author">';
                    echo '<span class="sr-only">Written by: </span>';
                    echo implode( ', <br class="is-hidden--desktop">', $authors );
                    echo '</span>';
                  }
                ?>
              </div>

              <div class="post__excerpt"><?php the_excerpt(); ?></div>

              <div class="post__read-more text--md">
                <?php _e('Read More', 'mayo'); ?>
                <span class="sr-only">
                  &ndash; <?php the_title(); ?>
                </span>
              </div>
            </div>
          </a>
        </div>
      <?php endwhile; ?>
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

  <?php else: ?>
    <p><?php __('Sorry, no posts matched your criteria.', 'mayo'); ?></p>
  <?php endif; ?>
</div>
