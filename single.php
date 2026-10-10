<?php
/**
 * Single post.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

get_header();
$has_sidebar = websource_theme_get_area('sidebar');
?>
<div class="wsx-container">
  <?php websource_theme_breadcrumb(); ?>
  <div class="<?php echo esc_attr($has_sidebar !== '' ? 'wsx-cols wsx-cols--right' : ''); ?>">
    <div id="content" class="wsx-cols__main min-w-0">
      <?php while (have_posts()) : ?>
            <?php the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('wsx-article'); ?>>
          <header class="wsx-page-header wsx-article__header">
            <h1 class="wsx-h1"><?php the_title(); ?></h1>
            <p class="wsx-article__meta">
                <?php echo websource_theme_posted_on(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                <?php if (get_the_author() !== '') : ?>
              <span aria-hidden="true">·</span>
              <span><?php the_author(); ?></span>
                <?php endif; ?>
                <?php if (has_category()) : ?>
                <span aria-hidden="true">·</span>
                <span class="wsx-article__cats"><?php the_category(', '); ?></span>
                <?php endif; ?>
            </p>
          </header>
            <?php if (has_post_thumbnail()) : ?>
            <figure class="wsx-article__cover"><?php the_post_thumbnail('large', ['fetchpriority' => 'high']); ?></figure>
            <?php endif; ?>
          <div class="wsx-entry">
            <?php
            the_content();
            wp_link_pages(['before' => '<nav class="wsx-page-links">' . esc_html__('Pages:', 'websource'), 'after' => '</nav>']);
            ?>
          </div>
            <?php the_tags('<p class="wsx-article__tags">', ' ', '</p>'); ?>
          <nav class="wsx-post-nav" aria-label="<?php esc_attr_e('Posts', 'websource'); ?>">
            <div class="wsx-post-nav__prev"><?php previous_post_link('%link', '<span class="wsx-flip" aria-hidden="true">←</span> %title'); ?></div>
            <div class="wsx-post-nav__next"><?php next_post_link('%link', '%title <span class="wsx-flip" aria-hidden="true">→</span>'); ?></div>
          </nav>
        </article>
            <?php
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
            ?>
      <?php endwhile; ?>
    </div>
    <?php if ($has_sidebar !== '') : ?>
      <aside id="right-column" class="wsx-cols__side wsx-sidebar wsx-sidebar--static"><?php echo $has_sidebar; // phpcs:ignore WordPress.Security.EscapeOutput ?></aside>
    <?php endif; ?>
  </div>
</div>
<?php
get_footer();
