<?php
/**
 * Left column of the shop: widgets of the “Shop — left column” area, or the category list by default.
 * On mobile it becomes a drawer opened by the “Filter” button of the toolbar.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

$html = websource_theme_get_area('shop-sidebar');
?>
<aside id="left-column" class="wsx-cols__side wsx-sidebar" aria-label="<?php esc_attr_e('Filters', 'websource'); ?>">
  <div class="wsx-sidebar__head">
    <span class="wsx-drawer__title"><?php esc_html_e('Filter', 'websource'); ?></span>
    <button type="button" class="wsx-hicon" data-wsx-drawer-close aria-label="<?php esc_attr_e('Close', 'websource'); ?>"><?php echo websource_theme_icon('close'); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
  </div>
  <?php if ($html !== '') : ?>
        <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
  <?php else : ?>
    <section class="wsx-widget wsx-cattree">
      <h2 class="wsx-widget__title"><?php esc_html_e('Categories', 'websource'); ?></h2>
      <ul class="wsx-cattree__list">
        <?php
        wp_list_categories([
            'taxonomy' => 'product_cat',
            'title_li' => '',
            'hide_empty' => true,
            'show_count' => false,
            'exclude' => (string) get_option('default_product_cat'),
            'current_category' => is_product_category() ? get_queried_object_id() : 0,
        ]);
        ?>
      </ul>
    </section>
  <?php endif; ?>
</aside>
