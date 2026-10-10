<?php
/**
 * Post card (blog lists, search results, home page).
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('wsx-post-card wsx-card'); ?>>
  <?php if (has_post_thumbnail()) : ?>
    <a class="wsx-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
        <?php the_post_thumbnail('medium_large', ['loading' => 'lazy', 'alt' => '']); ?>
    </a>
  <?php endif; ?>
  <div class="wsx-post-card__body">
    <p class="wsx-post-card__meta">
      <?php echo websource_theme_posted_on(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
      <?php if (get_post_type() !== 'post') : ?>
        <span class="wsx-post-card__type"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?></span>
      <?php endif; ?>
    </p>
    <h2 class="wsx-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="wsx-post-card__excerpt"><?php the_excerpt(); ?></div>
    <a class="wsx-post-card__more" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(sprintf(/* translators: %s: post title */ __('Read “%s”', 'websource'), get_the_title())); ?>"><?php esc_html_e('Read more', 'websource'); ?> <span class="wsx-flip" aria-hidden="true">→</span></a>
  </div>
</article>
