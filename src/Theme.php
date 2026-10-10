<?php

namespace Websource\Theme;

defined('ABSPATH') || exit;

/**
 * Theme setup: supports, menu locations, widget areas, body classes.
 */
final class Theme
{
    /** Menu locations (PrestaShop hook they stand for, in the description). */
    public static function menus(): array
    {
        return [
            'primary' => __('Main menu (under the header)', 'websource'),
            'topbar' => __('Top bar links (left)', 'websource'),
            'footer' => __('Footer links', 'websource'),
            'legal' => __('Legal links (bottom bar)', 'websource'),
        ];
    }

    /**
     * Widget areas. Key = area id, also the suffix of the `websource_theme_area_<id>` action.
     *
     * @return array<string, array{0:string,1:string}>
     */
    public static function areas(): array
    {
        return [
            'banner' => [__('Banner (very top)', 'websource'), __('Full-width strip above the header (PrestaShop hook displayBanner).', 'websource')],
            'topbar-left' => [__('Top bar — left', 'websource'), __('Small links or contact details above the logo (displayNav1).', 'websource')],
            'topbar-right' => [__('Top bar — right', 'websource'), __('Language or currency switchers, right of the top bar (displayNav2).', 'websource')],
            'header-search' => [__('Header — search', 'websource'), __('Replaces the built-in search field next to the logo (displayTop).', 'websource')],
            'header-menu' => [__('Header — menu row', 'websource'), __('Replaces the main menu under the header, e.g. with a mega menu (displayNavFullWidth).', 'websource')],
            'wrapper-top' => [__('Content — top, full width', 'websource'), __('Under the header, before the breadcrumb (displayWrapperTop).', 'websource')],
            'home' => [__('Home page', 'websource'), __('Sections of the home page, under the page content (displayHome).', 'websource')],
            'shop-sidebar' => [__('Shop — left column', 'websource'), __('Filters and categories next to the product list (displayLeftColumn).', 'websource')],
            'sidebar' => [__('Blog — right column', 'websource'), __('Column shown next to posts and blog archives (displayRightColumn).', 'websource')],
            'product-reassurance' => [__('Product — reassurance', 'websource'), __('Under the add-to-cart button on product pages (displayReassurance).', 'websource')],
            'product-footer' => [__('Product — bottom', 'websource'), __('Under the product details and related products (displayFooterProduct).', 'websource')],
            'cart-footer' => [__('Cart — bottom', 'websource'), __('Under the cart (displayShoppingCartFooter).', 'websource')],
            'wrapper-bottom' => [__('Content — bottom, full width', 'websource'), __('Just above the footer (displayWrapperBottom).', 'websource')],
            'footer-before' => [__('Footer — strip above', 'websource'), __('Coloured strip stuck to the footer, e.g. newsletter (displayFooterBefore).', 'websource')],
            'footer-1' => [__('Footer — column 1', 'websource'), __('First footer column (displayFooter).', 'websource')],
            'footer-2' => [__('Footer — column 2', 'websource'), __('Second footer column (displayFooter).', 'websource')],
            'footer-3' => [__('Footer — column 3', 'websource'), __('Third footer column (displayFooter).', 'websource')],
            'footer-after' => [__('Footer — bottom bar', 'websource'), __('Right of the copyright line (displayFooterAfter).', 'websource')],
        ];
    }

    public static function boot(): void
    {
        add_action('after_setup_theme', [self::class, 'setup']);
        add_action('widgets_init', [self::class, 'registerAreas']);
        add_filter('body_class', [self::class, 'bodyClasses']);
        add_filter('excerpt_more', static function () {
            return '…';
        });
        add_filter('excerpt_length', static function () {
            return 28;
        });
        add_filter('nav_menu_css_class', [self::class, 'menuItemClasses'], 10, 4);

        Assets::boot();
        Tokens::boot();
        Customizer::boot();
        Editor::boot();
        Shop::boot();
    }

    public static function setup(): void
    {
        // get_template_directory() and not __DIR__: WordPress compares this path with the theme folder (symlinked installs).
        load_theme_textdomain('websource', get_template_directory() . '/languages');

        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets']);
        add_theme_support('custom-logo', ['height' => 56, 'width' => 220, 'flex-height' => true, 'flex-width' => true]);
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        add_theme_support('editor-styles');
        add_theme_support('custom-background', ['default-color' => 'ffffff']);
        // Header image: displayed behind the welcome block of the home page (template-parts/home/hero.php).
        add_theme_support('custom-header', ['width' => 1600, 'height' => 640, 'flex-width' => true, 'flex-height' => true, 'header-text' => false]);
        add_editor_style(['assets/css/theme.css', 'assets/css/editor.css']);

        add_theme_support('woocommerce', [
            'thumbnail_image_width' => 452,
            'single_image_width' => 800,
            'product_grid' => ['default_rows' => 4, 'min_rows' => 1, 'default_columns' => 4, 'min_columns' => 2, 'max_columns' => 5],
        ]);
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');

        register_nav_menus(self::menus());

        $GLOBALS['content_width'] = 1280;
    }

    public static function registerAreas(): void
    {
        foreach (self::areas() as $id => $labels) {
            $footer = strpos($id, 'footer-') === 0 && $id !== 'footer-before' && $id !== 'footer-after';
            register_sidebar([
                'name' => $labels[0],
                'id' => 'wsx-' . $id,
                'description' => $labels[1],
                'before_widget' => '<section id="%1$s" class="wsx-widget %2$s">',
                'after_widget' => '</section>',
                'before_title' => $footer ? '<h2 class="wsx-widget__title wsx-footer__title">' : '<h2 class="wsx-widget__title">',
                'after_title' => '</h2>',
            ]);
        }
    }

    /**
     * Layout classes read by the CSS (same names as the PrestaShop theme): wsx-dh-*, wsx-mh-*, wsx-mn-*, wsx-ft-*.
     */
    public static function bodyClasses(array $classes): array
    {
        $s = Settings::all();
        $classes[] = 'wsx';
        $classes[] = 'wsx-dh-' . $s['header_desktop'];
        $classes[] = 'wsx-mh-' . $s['header_mobile'];
        $classes[] = 'wsx-mn-' . $s['mobile_nav'];
        $classes[] = 'wsx-ft-' . $s['footer_style'];
        $classes[] = 'wsx-page-' . Assets::pageType();

        return $classes;
    }

    public static function menuItemClasses($classes, $item, $args, $depth)
    {
        if (isset($args->theme_location) && $args->theme_location === 'primary') {
            $classes[] = $depth === 0 ? 'wsx-menu__item' : 'wsx-menu__subitem';
        }

        return $classes;
    }
}
