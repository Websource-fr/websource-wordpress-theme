<?php
/**
 * Site footer and mobile tab bar.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

websource_theme_area('wrapper-bottom', '<div class="wsx-wrapper-bottom">', '</div>');
?>
</main>
<footer id="footer">
<?php get_template_part('template-parts/footer'); ?>
</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
