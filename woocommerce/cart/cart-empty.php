<?php
/**
 * Empty cart.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;
?>
<div class="wsx-empty">
  <span class="wsx-empty__icon"><?php echo websource_theme_icon('cart', 40); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
  <?php
  /*
   * @hooked wc_empty_cart_message - 10
   */
  do_action('woocommerce_cart_is_empty');
    ?>
  <?php if (wc_get_page_id('shop') > 0) : ?>
    <p class="return-to-shop">
      <a class="button wc-backward wsx-btn wsx-btn--primary" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
        <?php echo esc_html(apply_filters('woocommerce_return_to_shop_text', __('Return to shop', 'websource'))); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>
      </a>
    </p>
  <?php endif; ?>
</div>
