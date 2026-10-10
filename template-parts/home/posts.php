<?php
/**
 * Home: latest posts.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

$query = new WP_Query(['post_type' => 'post', 'posts_per_page' => 3, 'ignore_sticky_posts' => true, 'no_found_rows' => true]);
if (!$query->have_posts()) {
    return;
}
?>
<section class="wsx-section">
  <div class="wsx-container">
    <div class="wsx-section__head">
      <h2 class="wsx-section__title"><?php esc_html_e('From the blog', 'websource'); ?></h2>
    </div>
    <div class="wsx-posts wsx-posts--grid">
      <?php
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/post-card');
        }
        wp_reset_postdata();
        ?>
    </div>
  </div>
</section>
