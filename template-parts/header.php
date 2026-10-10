<?php
/**
 * Header: banner, top bar, logo, search, actions, main menu.
 * The arrangement (desktop and mobile) comes from the wsx-dh-* / wsx-mh-* / wsx-mn-* classes on <body>.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

websource_theme_area('banner', '<div class="wsx-banner bg-ink text-sm text-white">', '</div>');

$topbar_left = websource_theme_get_area('topbar-left');
if ($topbar_left === '' && has_nav_menu('topbar')) {
    $topbar_left = wp_nav_menu(['theme_location' => 'topbar', 'container' => false, 'menu_class' => 'wsx-inline-menu', 'depth' => 1, 'echo' => false, 'fallback_cb' => false]);
}
$topbar_right = websource_theme_get_area('topbar-right');
// Search of the header: the `header-search` area (a search extension), otherwise the form of the theme.
$search = websource_theme_get_area('header-search');
if ($search === '') {
    $search = websource_theme_search_form('header');
}
?>
<div class="wsx-hdr wsx-container">
  <?php if ($topbar_left !== '' || $topbar_right !== '') : ?>
    <div class="wsx-hdr__util text-sm" aria-label="<?php esc_attr_e('Secondary navigation', 'websource'); ?>">
      <div class="wsx-hdr__util-l"><?php echo $topbar_left; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
      <div class="wsx-hdr__util-r"><?php echo $topbar_right; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
    </div>
  <?php endif; ?>

  <div class="wsx-hdr__actions text-sm">
    <?php
    /**
     * Header actions (desktop). As soon as a callback is hooked here, the default account and cart links are not
     * printed; to replace only one of them, use the `websource_theme_header_account` / `websource_theme_header_cart`
     * filters instead.
     */
    do_action('websource_theme_header_actions');
    if (!has_action('websource_theme_header_actions')) {
        echo websource_theme_header_account('desktop'); // phpcs:ignore WordPress.Security.EscapeOutput
        echo websource_theme_header_cart('desktop'); // phpcs:ignore WordPress.Security.EscapeOutput
    }
    ?>
  </div>

  <div class="wsx-hdr__main">
    <div class="wsx-hdr__l">
      <button type="button" class="wsx-hicon wsx-hicon--burger" data-wsx-drawer-open="wsx-drawer" aria-controls="wsx-drawer" aria-expanded="false" aria-label="<?php esc_attr_e('Menu', 'websource'); ?>">
        <?php echo websource_theme_icon('menu'); // phpcs:ignore WordPress.Security.EscapeOutput ?>
      </button>
      <?php if ($search !== '') : ?>
      <button type="button" class="wsx-loupe wsx-hicon" aria-controls="wsx-search" aria-expanded="false" aria-label="<?php esc_attr_e('Search', 'websource'); ?>">
        <?php echo websource_theme_icon('search'); // phpcs:ignore WordPress.Security.EscapeOutput ?>
      </button>
      <?php endif; ?>
    </div>
    <div class="wsx-hdr__logo">
      <?php if (is_front_page()) : ?>
        <h1 class="m-0 text-base"><?php echo websource_theme_logo(); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
      <?php else : ?>
        <?php echo websource_theme_logo(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
      <?php endif; ?>
    </div>
    <div class="wsx-hdr__r">
      <?php
      echo websource_theme_header_account('mobile'); // phpcs:ignore WordPress.Security.EscapeOutput
      echo websource_theme_header_cart('mobile'); // phpcs:ignore WordPress.Security.EscapeOutput
      ?>
    </div>
  </div>

  <?php if ($search !== '') : ?>
  <div class="wsx-hdr__search" id="wsx-search">
    <?php echo $search; // phpcs:ignore WordPress.Security.EscapeOutput ?>
  </div>
  <?php endif; ?>

  <div class="wsx-hdr__menu">
    <?php
    $menu = websource_theme_get_area('header-menu');
    if ($menu !== '') {
        echo $menu; // phpcs:ignore WordPress.Security.EscapeOutput
    } elseif (has_nav_menu('primary')) {
        wp_nav_menu([
            'theme_location' => 'primary',
            'container' => 'nav',
            'container_class' => 'wsx-menu',
            'container_aria_label' => __('Main menu', 'websource'),
            'menu_class' => 'wsx-menu__list',
            'depth' => 3,
            'fallback_cb' => false,
        ]);
    }
    ?>
  </div>
</div>

<?php // Mobile drawer: main menu, opened by the “Menu” buttons (header). ?>
<div class="wsx-drawer" id="wsx-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Menu', 'websource'); ?>" hidden>
  <div class="wsx-drawer__panel">
    <div class="wsx-drawer__head">
      <span class="wsx-drawer__title"><?php esc_html_e('Menu', 'websource'); ?></span>
      <button type="button" class="wsx-hicon" data-wsx-drawer-close aria-label="<?php esc_attr_e('Close', 'websource'); ?>"><?php echo websource_theme_icon('close'); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
    </div>
    <?php
    if (has_nav_menu('primary')) {
        wp_nav_menu(['theme_location' => 'primary', 'container' => 'nav', 'container_class' => 'wsx-drawer__nav', 'container_aria_label' => __('Main menu', 'websource'), 'menu_class' => 'wsx-drawer__list', 'depth' => 3, 'fallback_cb' => false]);
    }
    if (has_nav_menu('topbar')) {
        wp_nav_menu(['theme_location' => 'topbar', 'container' => 'nav', 'container_class' => 'wsx-drawer__nav wsx-drawer__nav--secondary', 'menu_class' => 'wsx-drawer__list', 'depth' => 1, 'fallback_cb' => false]);
    }
    /** Fires at the bottom of the mobile menu drawer. */
    do_action('websource_theme_drawer');
    ?>
  </div>
</div>
