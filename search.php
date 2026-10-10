<?php
/**
 * Search results (site-wide search; product searches use the shop template).
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

get_header();
global $wp_query;
?>
<div class="wsx-container">
  <?php websource_theme_breadcrumb(); ?>
  <div id="content">
    <header class="wsx-page-header">
      <h1 class="wsx-h1"><?php echo esc_html(sprintf(/* translators: %s: search terms */ __('Search results for “%s”', 'websource'), get_search_query())); ?></h1>
      <p class="wsx-page-header__desc"><?php echo esc_html(sprintf(/* translators: %d: number of results */ _n('%d result', '%d results', (int) $wp_query->found_posts, 'websource'), (int) $wp_query->found_posts)); ?></p>
    </header>
    <div class="wsx-search-again"><?php echo websource_theme_search_form('results'); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
    <?php if (have_posts()) : ?>
      <div class="wsx-posts wsx-posts--grid">
        <?php
        while (have_posts()) {
            the_post();
            get_template_part('template-parts/post-card');
        }
        ?>
      </div>
        <?php websource_theme_pagination(); ?>
    <?php else : ?>
      <div class="wsx-empty">
        <span class="wsx-empty__icon"><?php echo websource_theme_icon('search', 40); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <p class="wsx-empty__text"><?php esc_html_e('No result matches your search. Try other words or check the spelling.', 'websource'); ?></p>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php
get_footer();
