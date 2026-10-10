<?php
/**
 * Home: latest products.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

$html = do_shortcode('[products limit="8" columns="4" orderby="date" order="DESC"]');
if (trim(wp_strip_all_tags($html)) === '') {
    return;
}
?>
<section class="wsx-section">
  <div class="wsx-container">
    <div class="wsx-section__head">
      <h2 class="wsx-section__title"><?php esc_html_e('New products', 'websource'); ?></h2>
      <a class="wsx-section__more" href="<?php echo esc_url(add_query_arg('orderby', 'date', wc_get_page_permalink('shop'))); ?>"><?php esc_html_e('All new products', 'websource'); ?> <span class="wsx-flip" aria-hidden="true">→</span></a>
    </div>
    <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
  </div>
</section>
