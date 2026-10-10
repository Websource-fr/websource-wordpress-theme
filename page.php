<?php
/**
 * Page (also used by the cart, checkout and account pages of WooCommerce).
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

get_header();
?>
<div class="wsx-container">
  <?php websource_theme_breadcrumb(); ?>
  <div id="content">
    <?php while (have_posts()) : ?>
        <?php the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('wsx-pagecontent'); ?>>
        <header class="wsx-page-header">
          <h1 class="wsx-h1"><?php the_title(); ?></h1>
        </header>
        <div class="wsx-entry">
          <?php
            the_content();
            wp_link_pages(['before' => '<nav class="wsx-page-links">' . esc_html__('Pages:', 'websource'), 'after' => '</nav>']);
            ?>
        </div>
      </article>
        <?php
        if (comments_open() || get_comments_number()) {
            comments_template();
        }
        ?>
    <?php endwhile; ?>
  </div>
</div>
<?php
get_footer();
