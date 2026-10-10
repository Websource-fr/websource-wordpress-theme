<?php
/**
 * Home: product categories as tiles.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

$terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 8, 'exclude' => [(int) get_option('default_product_cat')]]);
if (is_wp_error($terms) || !$terms) {
    return;
}
?>
<section class="wsx-section wsx-section--tint">
  <div class="wsx-container">
    <div class="wsx-section__head">
      <h2 class="wsx-section__title"><?php esc_html_e('Shop by category', 'websource'); ?></h2>
    </div>
    <ul class="wsx-cats">
      <?php foreach ($terms as $term) : ?>
            <?php $thumb = (int) get_term_meta($term->term_id, 'thumbnail_id', true); ?>
        <li>
          <a class="wsx-cats__item wsx-card" href="<?php echo esc_url(get_term_link($term)); ?>">
            <?php if ($thumb) : ?>
                <?php echo wp_get_attachment_image($thumb, 'woocommerce_thumbnail', false, ['class' => 'wsx-cats__img', 'loading' => 'lazy', 'alt' => '']); ?>
            <?php endif; ?>
            <span class="wsx-cats__name"><?php echo esc_html($term->name); ?></span>
            <span class="wsx-cats__count"><?php echo esc_html(sprintf(/* translators: %d: number of products */ _n('%d product', '%d products', $term->count, 'websource'), $term->count)); ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
