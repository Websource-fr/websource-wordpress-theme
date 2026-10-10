<?php
/**
 * Footer: strip above, columns (shop presentation + widget columns), bottom bar.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

$columns = [];
foreach (['footer-1', 'footer-2', 'footer-3'] as $area) {
    $html = websource_theme_get_area($area);
    if ($html !== '') {
        $columns[] = $html;
    }
}
if (!$columns && has_nav_menu('footer')) {
    $columns[] = '<section class="wsx-widget"><h2 class="wsx-widget__title wsx-footer__title">' . esc_html__('Information', 'websource') . '</h2>'
        . wp_nav_menu(['theme_location' => 'footer', 'container' => false, 'menu_class' => 'wsx-footer__links', 'depth' => 1, 'echo' => false, 'fallback_cb' => false]) . '</section>';
}
$text = websource_theme_setting('footer_text');
if ($text === '') {
    $text = get_bloginfo('description');
}
$copyright = websource_theme_setting('copyright');
if ($copyright === '') {
    $copyright = '© ' . gmdate('Y') . ' ' . get_bloginfo('name');
}
/** @param string $copyright Copyright line of the bottom bar (plain text or safe HTML). */
$copyright = apply_filters('websource_theme_copyright', esc_html($copyright));
?>
<div class="wsx-footer mt-10">
  <?php websource_theme_area('footer-before', '<div class="wsx-footer__before"><div class="wsx-container">', '</div></div>'); ?>
  <div class="wsx-footer__body">
    <div class="wsx-container wsx-footer__main wsx-footer__main--info" style="--wsx-footer-cols:<?php echo (int) count($columns); ?>">
      <div class="wsx-footer__about">
        <div class="wsx-footer__brand"><?php echo websource_theme_logo(); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
        <?php if ($text !== '') : ?>
          <p class="wsx-footer__desc"><?php echo nl2br(esc_html($text)); ?></p>
        <?php endif; ?>
        <?php
        /** Fires in the first footer column, under the presentation text (reviews, social links…). */
        do_action('websource_theme_footer_about');
        ?>
      </div>
      <?php foreach ($columns as $column) : ?>
        <div class="wsx-footer__col"><?php echo $column; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="wsx-footer__bar">
    <div class="wsx-container flex flex-wrap items-center justify-between gap-x-6 gap-y-1 py-4 text-sm">
      <div class="wsx-footer__copy"><?php echo wp_kses_post($copyright); ?> · <a href="<?php echo esc_url(wp_get_theme()->get('ThemeURI')); ?>"><?php esc_html_e('Websource theme', 'websource'); ?></a></div>
      <div class="wsx-footer__legal flex flex-wrap items-center gap-x-5 gap-y-1">
        <?php
        if (has_nav_menu('legal')) {
            wp_nav_menu(['theme_location' => 'legal', 'container' => false, 'menu_class' => 'wsx-inline-menu', 'depth' => 1, 'fallback_cb' => false]);
        }
        websource_theme_area('footer-after');
        ?>
      </div>
    </div>
  </div>
</div>
