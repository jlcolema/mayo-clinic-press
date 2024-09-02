<?php
  /**
   * Kids Hub Hero template
   *
   * @param   array $block The block settings and attributes.
   * @param   string $content The block inner HTML (empty).
   * @param   bool $is_preview True during AJAX preview.
   * @param   (int|string) $post_id The post ID this block is saved to.
   */

  $bg_is_image = get_field( 'is_background_image' );
  if ( $bg_is_image ) {
    $bg_img = get_field( 'bg_img' );
    $bg_img_mobile = get_field( 'bg_img_mobile' );
  } else {
    $bg_color = get_field( 'bg_color' );
  }
  $title_is_image = get_field( 'hero_is_image' );
  $title = get_the_title();
  if ( $title_is_image ) {
    $title_image = get_field( 'hero_title_image' );
  } elseif ( get_field( 'hero_title' ) ) {
    $title = get_field( 'hero_title' );
  }
?>

<div class="hub-hero<?php if ( !$bg_is_image ) : ?> color-<?php echo esc_html( $bg_color ); ?><?php endif; ?>" style="--featured-padding: 0px;<?php if ( $bg_is_image ) : ?>color: var(--color-white);<?php else : ?>background-color: var(--color-<?php echo esc_html( $bg_color ); ?>);<?php endif; ?>">
  <?php if ($bg_img) : ?><div class="hub-hero__image<?php if ($bg_img_mobile) : ?> is-hidden--tablet-down<?php endif; ?>" style="background-image: url(<?php echo esc_url( $bg_img ); ?>);"></div><?php endif; ?>
  <?php if ($bg_img_mobile) : ?><div class="hub-hero__image is-hidden--desktop" style="background-image: url(<?php echo esc_url( $bg_img_mobile ); ?>);"></div><?php endif; ?>
  <h1 class="hub-hero__title<?php if ( $title_is_image ) : ?> hub-hero__title--image<?php endif; ?>">
    <?php
      if ( $title_is_image ) {
        echo '<img src="' . $title_image . '" alt="" />';
      } else {
        echo $title;
      };
    ?>
  </h1>
  <svg class="icon icon--book-swoosh" aria-hidden="true" focusable="false"><use xlink:href="#book-swoosh"></use></svg>
</div>
