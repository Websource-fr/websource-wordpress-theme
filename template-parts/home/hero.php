<?php
/**
 * Home: welcome block (site name, tagline, link to the shop or the blog).
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

$has_shop = class_exists('WooCommerce');
$url = $has_shop ? wc_get_page_permalink('shop') : (get_option('page_for_posts') ? get_permalink((int) get_option('page_for_posts')) : '');
?>
<section class="wsx-hero">
  <div class="wsx-container">
    <div class="wsx-hero__box<?php echo has_header_image() ? ' wsx-hero__box--media' : ''; ?>">
      <?php if (has_header_image()) : ?>
            <?php the_header_image_tag(['class' => 'wsx-hero__media', 'alt' => '', 'loading' => 'eager']); ?>
      <?php endif; ?>
      <p class="wsx-hero__eyebrow"><?php echo esc_html(get_bloginfo('name')); ?></p>
      <h2 class="wsx-hero__title"><?php echo esc_html(get_bloginfo('description') ?: __('Welcome to our shop', 'websource')); ?></h2>
      <?php if ($url) : ?>
        <p class="wsx-hero__cta"><a class="wsx-btn wsx-btn--primary wsx-btn--lg" href="<?php echo esc_url($url); ?>"><?php echo esc_html($has_shop ? __('Browse the shop', 'websource') : __('Read the blog', 'websource')); ?></a></p>
      <?php endif; ?>
    </div>
  </div>
</section>
