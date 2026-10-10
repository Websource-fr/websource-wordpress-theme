<?php
/**
 * Blog index and archives (categories, tags, dates, authors).
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
      <header class="wsx-page-header">
        <h1 class="wsx-h1">
          <?php
            if (is_home()) {
                echo esc_html(get_option('page_for_posts') ? get_the_title((int) get_option('page_for_posts')) : __('Blog', 'websource'));
            } else {
                the_archive_title();
            }
            ?>
        </h1>
        <?php the_archive_description('<div class="wsx-page-header__desc">', '</div>'); ?>
      </header>
      <?php if (have_posts()) : ?>
        <div class="wsx-posts">
            <?php
            while (have_posts()) {
                the_post();
                get_template_part('template-parts/post-card');
            }
            ?>
        </div>
            <?php websource_theme_pagination(); ?>
      <?php else : ?>
        <p class="wsx-empty__text"><?php esc_html_e('Nothing has been published here yet.', 'websource'); ?></p>
      <?php endif; ?>
    </div>
    <?php if ($has_sidebar !== '') : ?>
      <aside id="right-column" class="wsx-cols__side wsx-sidebar wsx-sidebar--static"><?php echo $has_sidebar; // phpcs:ignore WordPress.Security.EscapeOutput ?></aside>
    <?php endif; ?>
  </div>
</div>
<?php
get_footer();
