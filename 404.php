<?php
/**
 * Page not found.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

get_header();
$has_shop = class_exists('WooCommerce');
?>
<div class="wsx-container">
  <div id="content" class="wsx-404">
    <p class="wsx-404__code" aria-hidden="true">404</p>
    <h1 class="wsx-h1"><?php esc_html_e('This page cannot be found', 'websource'); ?></h1>
    <p class="wsx-404__text"><?php esc_html_e('The address may be wrong, or the page may have been moved. Search for what you need, or go back to the home page.', 'websource'); ?></p>
    <div class="wsx-404__search"><?php echo websource_theme_search_form('404'); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
    <p class="wsx-404__actions">
      <a class="wsx-btn wsx-btn--primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to the home page', 'websource'); ?></a>
      <?php if ($has_shop) : ?>
        <a class="wsx-btn wsx-btn--ghost" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('Browse the shop', 'websource'); ?></a>
      <?php endif; ?>
    </p>
  </div>
</div>
<?php
get_footer();
