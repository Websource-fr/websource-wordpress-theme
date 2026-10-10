<?php
/**
 * Product details: description, additional information, reviews and the tabs added by plugins,
 * shown as tabs (data-details-mode, handled by assets/js/product.js).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined('ABSPATH') || exit;

$product_tabs = apply_filters('woocommerce_product_tabs', []);

if (empty($product_tabs)) {
    return;
}

$websource_theme_mode = \Websource\Theme\Shop::productLayout()['details_mode'];
$websource_theme_first = true;
?>
<div class="wsx-details" data-wsx-details data-details-mode="<?php echo esc_attr($websource_theme_mode); ?>">
  <div class="wsx-dtabs" role="tablist" aria-label="<?php esc_attr_e('Product details', 'websource'); ?>">
    <?php foreach ($product_tabs as $key => $product_tab) : ?>
      <button type="button" class="wsx-dtabs__btn <?php echo esc_attr($key); ?>_tab" role="tab" id="tab-title-<?php echo esc_attr($key); ?>" aria-controls="tab-<?php echo esc_attr($key); ?>" aria-selected="<?php echo $websource_theme_first ? 'true' : 'false'; ?>" tabindex="<?php echo $websource_theme_first ? '0' : '-1'; ?>" data-wsx-navtab="<?php echo esc_attr($key); ?>">
        <?php echo wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key)); ?>
      </button>
        <?php $websource_theme_first = false; ?>
    <?php endforeach; ?>
  </div>
  <?php $websource_theme_first = true; ?>
  <?php foreach ($product_tabs as $key => $product_tab) : ?>
        <?php
        $websource_theme_title = wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key));
        $websource_theme_open = $websource_theme_first;
        ?>
    <section class="wsx-dpanel" data-wsx-panel="<?php echo esc_attr($key); ?>">
      <h2 class="wsx-dpanel__static"><?php echo $websource_theme_title; // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
      <div class="wsx-dpanel__body woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr($key); ?> entry-content" id="tab-<?php echo esc_attr($key); ?>"<?php echo $websource_theme_open ? '' : ' hidden'; ?>>
        <?php
        if (isset($product_tab['callback'])) {
            call_user_func($product_tab['callback'], $key, $product_tab);
        }
        ?>
      </div>
    </section>
        <?php $websource_theme_first = false; ?>
  <?php endforeach; ?>
  <noscript><style>.wsx-dpanel__body[hidden]{display:block!important}.wsx-dtabs{display:none!important}.wsx-dpanel__static{display:block!important}</style></noscript>
  <?php do_action('woocommerce_product_after_tabs'); ?>
</div>
