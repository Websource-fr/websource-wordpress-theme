<?php
/**
 * Search form, called by get_search_form(): products when WooCommerce is active, the whole site otherwise.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

$websource_theme_suffix = isset($args['websource_theme_id']) ? (string) $args['websource_theme_id'] : 'w' . wp_unique_id();
$websource_theme_field = 'wsx-search-field-' . sanitize_key($websource_theme_suffix);
$websource_theme_shop = class_exists('WooCommerce');
$websource_theme_label = $websource_theme_shop ? __('Search our catalogue', 'websource') : __('Search', 'websource');
?>
<form role="search" method="get" class="wsx-search" action="<?php echo esc_url(home_url('/')); ?>">
  <label class="sr-only" for="<?php echo esc_attr($websource_theme_field); ?>"><?php echo esc_html($websource_theme_label); ?></label>
  <input type="search" id="<?php echo esc_attr($websource_theme_field); ?>" class="wsx-search__input" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php echo esc_attr($websource_theme_label); ?>" autocomplete="off">
    <?php if ($websource_theme_shop) : ?>
  <input type="hidden" name="post_type" value="product">
    <?php endif; ?>
  <button type="submit" class="wsx-search__btn" aria-label="<?php esc_attr_e('Search', 'websource'); ?>"><?php echo websource_theme_icon('search', 20); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG built by the theme. ?></button>
</form>
