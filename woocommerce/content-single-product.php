<?php
/**
 * Product page, in four zones (top / media / info / below) laid out by one of six presets
 * (class wsx-pl-<preset>, see assets/src/pages/product.css). Standard WooCommerce hooks, standard order.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput
    return;
}

$websource_theme_layout = \Websource\Theme\Shop::productLayout();
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('wsx-product wsx-pl-' . $websource_theme_layout['preset'], $product); ?> data-wsx-product data-details-mode="<?php echo esc_attr($websource_theme_layout['details_mode']); ?>">

  <div class="wsx-pz wsx-pz--top">
    <?php
    /** Fires in the top zone of the product page (full width, above the gallery). Wrap your output in a .wsx-pb element. */
    do_action('websource_theme_product_top', $product);
    ?>
  </div>

  <div class="wsx-pz wsx-pz--media">
    <?php do_action('woocommerce_before_single_product_summary'); ?>
  </div>

  <div class="wsx-pz wsx-pz--info summary entry-summary">
    <?php do_action('woocommerce_single_product_summary'); ?>
  </div>

  <div class="wsx-pz wsx-pz--below">
    <?php do_action('woocommerce_after_single_product_summary'); ?>
  </div>
</div>

<?php do_action('woocommerce_after_single_product'); ?>
