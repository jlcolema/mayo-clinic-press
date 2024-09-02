<?php

/**
 * Post accordion template
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 * 
 * Field Structure:
 * 
 * - accordion_sections (Repeater)
 *   - section_title (Text)
 *   - accordions (Repeater)
 *     - accordion_title (Text)
 *     - accordion_content (Wysiwyg Editor)
 */

// Create id attribute allowing for custom "anchor" value
$id = 'post-accordion-' . $block['id'];
if (!empty($block['anchor'])) {
  $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values
$className = 'post-accordion';
if (!empty($block['className'])) {
  $className .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
  $className .= ' align' . $block['align'];
}
?>

<?php if ( have_rows('accordion_sections') ) : ?>
<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
  <?php

  // loop through rows (parent repeater)
  while( have_rows('accordion_sections') ): the_row(); ?>
  <div class="post-accordion__section">
    <?php

    // check if section has title
    if ( get_sub_field('section_title') ) : ?>
    <h2 class="has-heading-md-font-size"><?php the_sub_field('section_title'); ?></h2>
    <?php endif; ?>

    <div class="post-accordion accordion js-accordion" data-animation="on" data-component="accordion" data-autoclose="true">
      <?php

      // loop through rows (sub repeater)
      while( have_rows('accordions') ): the_row(); ?>
      <div class="accordion__item js-accordion__item">
        <div class="post-accordion__heading accordion__header reset js-accordion__trigger js-tab-focus">
          <?php the_sub_field('accordion_title'); ?>
          <svg class="icon accordion__icon-plus no-js:is-hidden" viewBox="0 0 20 20" aria-hidden="true"> <g class="icon__group" fill="none" stroke="currentColor"> <line x1="2" y1="10" x2="18" y2="10"></line> <line x1="10" y1="18" x2="10" y2="2"></line> </g> </svg>
        </div>
        <div class="accordion__panel js-accordion__panel">
          <?php the_sub_field('accordion_content'); ?>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
  <?php endwhile; ?>
</div>
<?php endif; ?>
