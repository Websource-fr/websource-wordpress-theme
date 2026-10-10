<?php
/**
 * Product card in lists (shop, categories, related products, shortcodes).
 * Same structure as the PrestaShop miniature: image + badges, then title, rating and price, then the button.
 * All the standard WooCommerce hooks still run, in the standard order.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

global $product;

if (!is_a($product, WC_Product::class) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('wsx-pm-wrap', $product); ?>>
  <article class="wsx-pm" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    <?php do_action('woocommerce_before_shop_loop_item'); ?>
    <div class="wsx-pm__media">
      <?php do_action('woocommerce_before_shop_loop_item_title'); ?>
    </div>
    <div class="wsx-pm__body">
      <?php
        do_action('woocommerce_shop_loop_item_title');
        do_action('woocommerce_after_shop_loop_item_title');
        ?>
    </div>
    <div class="wsx-pm__actions">
      <?php do_action('woocommerce_after_shop_loop_item'); ?>
    </div>
  </article>
</li>
