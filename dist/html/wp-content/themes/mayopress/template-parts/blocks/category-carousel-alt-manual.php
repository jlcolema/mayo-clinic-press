<?php

/**
 * Category carousel manual alt template
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// gets all pages to reference for image
$pages = get_pages();

$is_user_logged_in = is_user_logged_in();
$user_selected_cats = [];

if ($is_user_logged_in) {
  $user_id = get_current_user_id();
  $user_selected_cats_ids = get_user_meta($user_id, 'selected_categories', true);

  // Convert term IDs to category objects
  foreach ($user_selected_cats_ids as $term_id) {
    $term_object = get_term($term_id);
    if ($term_object instanceof WP_Term) {
      $user_selected_cats[] = $term_object;
    }
  }
}
$is_personalized = !empty($user_selected_cats);

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
$img_size = get_field('image_size');
$selected_cats = get_field('selected_categories');

// Modify title and subcopy if personalized
if ($is_personalized) {
  $title = str_replace('Popular', '', $title);
  $subcopy = "We publish on a diverse range of topics. Explore your selected topics and more!";
}

// sort alphabetically
usort($user_selected_cats, function($a, $b) {
  return strcmp($a->name, $b->name);
});
usort($selected_cats, function($a, $b) {
  return strcmp($a->name, $b->name);
});

// Combine user's selected categories with block's selected categories without duplicates
$all_cats = array_merge($user_selected_cats, $selected_cats);
$all_cats = array_unique($all_cats, SORT_REGULAR);

$display_cats = array_slice($all_cats, 0, count($selected_cats));
?>

<style>
  #personalized--tooltip-<?php echo $block['id']; ?> {
    display: none;
  }
  label:has(#personalized--tooltip-<?php echo $block['id']; ?>:checked) + .personalized--tooltip {
    display: block;
  }
</style>

<div id="<?php echo esc_attr($id); ?>" class="post-carousel-alt category-carousel-alt <?php echo esc_attr($className); ?>">
  <?php if ($title): ?>
    <header class="post-carousel-alt__header category-carousel-alt__header">
      <h2 class="post-carousel-alt__title heading--lg">
        <?php echo $title; ?>

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
      </h2>

      <?php if ( !empty( $subcopy ) ) : ?>
        <p><?php echo $subcopy; ?></p>
      <?php endif; ?>

      <span class="post-carousel-alt__link">
        <a href="/all-topics/" class="btn btn--primary">
          <?php echo _e( 'View All Topics', 'mayo' ); ?>
        </a>
      </span>
    </header>
  <?php endif; ?>

  <div class="slider" data-component="post-carousel-alt">
    <div class="slider__track">
    <?php foreach ( $display_cats as $cat ) :
      // Post variables
      $cat_name = $cat->name;
      $cat_slug = $cat->slug;
      $cat_description = $cat->description;
      $cat_link = get_term_link( $cat );
      $cat_id = $cat->term_id;

      // Check if this category is in the user's selected categories
      $is_user_category = in_array($cat_id, $user_selected_cats_ids);
    ?>
      <div class="slider__item">
        <?php foreach ( $pages as $list_page ) {
          if ( $list_page->post_name == $cat_slug ) {
            $cat_image = get_the_post_thumbnail($list_page->ID, $img_size);
          }
        }; ?>
        <div class="post-card__image">
            <?php echo $cat_image; ?>
        </div>
        <div class="post-card__content">
          <p class="personalized__badge<?php if ( $is_user_category ) : ?> personalized-post<?php endif; ?>" <?php if ( !$is_user_category ) : ?>aria-hidden="true"<?php endif; ?>><?php echo __('Selected Topic', 'mayo'); ?></p>
          <p class="slider-item__title has-heading-md-font-size"><?php echo $cat_name; ?></p>
          <p class="slider-item__desc"><?php echo $cat_description; ?></p>
          <a href="<?php echo $cat_link; ?>" class="btn"><?php echo sprintf( __( 'View %s', 'mayo' ), $cat_name ); ?></a>
        </div>
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
