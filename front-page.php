<?php
/**
 * Home page: content of the static front page (if any), the “Home page” area, then the built-in sections
 * (new products, sale products, categories, latest posts) when nothing else fills the page.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

if ('posts' === get_option('show_on_front')) {
    // Reading settings ask for the latest posts on the front page: the blog index is displayed.
    locate_template(['home.php', 'index.php'], true);
    return;
}

get_header();

$has_content = false;
$has_shop = class_exists('WooCommerce');
?>
<div id="content" class="wsx-home">
  <?php if (get_option('show_on_front') === 'page' && have_posts()) : ?>
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <?php if (trim((string) get_the_content()) !== '') : ?>
                <?php $has_content = true; ?>
        <div class="wsx-container"><div class="wsx-entry wsx-home__content"><?php the_content(); ?></div></div>
            <?php endif; ?>
        <?php endwhile; ?>
  <?php endif; ?>

  <?php
    if (websource_theme_area('home', '<div class="wsx-container wsx-home__area">', '</div>')) {
        $has_content = true;
    }

    $sections = [];
    if (!$has_content) {
        $sections[] = 'hero';
        if ($has_shop) {
            $sections[] = 'new_products';
            $sections[] = 'categories';
            $sections[] = 'sale_products';
        }
        $sections[] = 'posts';
    }
    /**
     * Built-in sections of the home page, in order. Default: none when the page has content or widgets.
     *
     * @param string[] $sections Among hero, new_products, categories, sale_products, posts.
     * @param bool     $has_content Whether the page content or the “Home page” area printed something.
     */
    $sections = (array) apply_filters('websource_theme_home_sections', $sections, $has_content);

    foreach ($sections as $section) {
        get_template_part('template-parts/home/' . sanitize_key(str_replace('_', '-', $section)));
    }
    ?>
</div>
<?php
get_footer();
