<?php

/**
 * Icon box template
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

  $color = get_field('color');
  $background_color = get_field('background_color');
  $position = get_field('position');
  $icon = get_field('icon');
  $icon_size = get_field('size');
  $className = 'icon-box';

  $className .= ' ' . 'icon-box--align-' . $position;
  if ($color) $className .= ' has-' . $color . '-color';
  if ($background_color) $className .= ' has-' . $background_color . '-background-color';
  if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
  }

  $show_personalized_offline = strpos($className, 'personalized_offline') !== false;

  if ($show_personalized_offline && is_user_logged_in()) return;
?>

<div class="<?php echo esc_attr($className); ?>"<?php if ($show_personalized_offline) : ?> data-component="personalization-redirect"<?php endif; ?>>
  <?php if ($icon): ?>
    <i class="icon icon--<?php echo $icon; ?> <?php echo $icon_size ? 'icon--' . $icon_size : ''; ?>">
      <?php echo get_the_svg($icon); ?>
    </i>
  <?php endif; ?>

<div class="icon-box__content">
  <InnerBlocks />
</div>
</div>
