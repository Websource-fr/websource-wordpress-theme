<?php
/**
 * Customer account navigation, with an icon per entry.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_account_navigation');
?>
<nav class="woocommerce-MyAccount-navigation wsx-account-nav" aria-label="<?php esc_attr_e('Account pages', 'websource'); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>">
  <ul>
    <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
      <li class="<?php echo wc_get_account_menu_item_classes($endpoint); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
        <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" <?php echo wc_is_current_account_menu_item($endpoint) ? 'aria-current="page"' : ''; ?>>
          <?php echo websource_theme_icon(\Websource\Theme\Shop::accountIcon($endpoint), 20); // phpcs:ignore WordPress.Security.EscapeOutput ?>
          <span><?php echo esc_html($label); ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
<?php do_action('woocommerce_after_account_navigation'); ?>
