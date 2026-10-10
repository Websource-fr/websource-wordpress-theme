<?php
/**
 * Home: products on sale.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

$html = do_shortcode('[products limit="4" columns="4" on_sale="true"]');
if (trim(wp_strip_all_tags($html)) === '') {
    return;
}
?>
<section class="wsx-section">
  <div class="wsx-container">
    <div class="wsx-section__head">
      <h2 class="wsx-section__title"><?php esc_html_e('On sale', 'websource'); ?></h2>
    </div>
    <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
  </div>
</section>
