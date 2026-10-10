<?php

namespace Websource\Theme;

defined('ABSPATH') || exit;

/**
 * Loads the base stylesheet everywhere and one stylesheet per page type, nothing else.
 */
final class Assets
{
    /** Page type => compiled file in assets/css/pages/. */
    const PAGES = ['home', 'shop', 'product', 'cart', 'checkout', 'account', 'post', 'blog', 'page', 'search', '404'];

    /** @var string|null */
    private static $type;

    public static function boot(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue'], 10);
        add_filter('woocommerce_enqueue_styles', [self::class, 'woocommerceStyles']);
        add_action('wp_enqueue_scripts', [self::class, 'trim'], 100);
    }

    /**
     * Type of the current page: home, shop, product, cart, checkout, account, post, blog, page, search or 404.
     */
    public static function pageType(): string
    {
        if (self::$type !== null) {
            return self::$type;
        }
        $woo = class_exists('WooCommerce');
        $type = 'page';
        if (is_404()) {
            $type = '404';
        } elseif ($woo && is_product()) {
            $type = 'product';
        } elseif ($woo && (is_shop() || is_product_taxonomy() || (is_search() && get_query_var('post_type') === 'product'))) {
            $type = 'shop';
        } elseif ($woo && (is_cart() || self::hasBlock('woocommerce/cart'))) {
            $type = 'cart';
        } elseif ($woo && (is_checkout() || self::hasBlock('woocommerce/checkout'))) {
            $type = 'checkout';
        } elseif ($woo && is_account_page()) {
            $type = 'account';
        } elseif (is_front_page()) {
            $type = 'home';
        } elseif (is_search()) {
            $type = 'search';
        } elseif (is_singular('post')) {
            $type = 'post';
        } elseif (is_home() || is_archive()) {
            $type = 'blog';
        } elseif (is_singular() && !is_page()) {
            $type = 'post';
        }

        /**
         * Page type used to pick the per-page stylesheet.
         *
         * @param string $type One of Assets::PAGES.
         */
        $filtered = (string) apply_filters('websource_theme_page_type', $type);
        if (did_action('wp')) {
            self::$type = in_array($filtered, self::PAGES, true) ? $filtered : $type;

            return self::$type;
        }

        return $type;
    }

    private static function hasBlock(string $block): bool
    {
        return is_singular() && has_block($block, get_queried_object_id());
    }

    public static function url(string $path): string
    {
        return get_template_directory_uri() . '/' . ltrim($path, '/');
    }

    private static function version(string $path): string
    {
        $file = WEBSOURCE_THEME_DIR . '/' . $path;

        return is_file($file) ? WEBSOURCE_THEME_VERSION . '.' . filemtime($file) : WEBSOURCE_THEME_VERSION;
    }

    public static function enqueue(): void
    {
        wp_enqueue_style('websource-theme', self::url('assets/css/theme.css'), [], self::version('assets/css/theme.css'));
        wp_style_add_data('websource-theme', 'rtl', false);

        $type = self::pageType();
        /**
         * Per-page stylesheets to load, relative to the theme folder.
         *
         * @param string[] $files handle suffix => path
         * @param string   $type  page type
         */
        $files = (array) apply_filters('websource_theme_page_styles', [$type => 'assets/css/pages/' . $type . '.css'], $type);
        foreach ($files as $suffix => $path) {
            if (is_file(WEBSOURCE_THEME_DIR . '/' . $path)) {
                wp_enqueue_style('websource-theme-' . sanitize_key($suffix), self::url($path), ['websource-theme'], self::version($path));
            }
        }

        wp_enqueue_script('websource-theme', self::url('assets/js/theme.js'), [], self::version('assets/js/theme.js'), ['strategy' => 'defer', 'in_footer' => true]);
        wp_localize_script('websource-theme', 'websourceTheme', [
            'cartUrl' => class_exists('WooCommerce') ? esc_url_raw(rest_url('wc/store/v1/cart')) : '',
        ]);
        if ($type === 'product') {
            wp_enqueue_script('websource-theme-product', self::url('assets/js/product.js'), [], self::version('assets/js/product.js'), ['strategy' => 'defer', 'in_footer' => true]);
        }
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }

    /**
     * WooCommerce's own stylesheets are replaced by the theme's (smaller, per page).
     */
    public static function woocommerceStyles($styles)
    {
        /** @param bool $keep Return true to keep WooCommerce's default CSS. */
        return apply_filters('websource_theme_keep_woocommerce_css', false) ? $styles : [];
    }

    /** Front-end styles that the theme already covers. */
    public static function trim(): void
    {
        if (apply_filters('websource_theme_keep_woocommerce_css', false)) {
            return;
        }
        wp_dequeue_style('classic-theme-styles');
    }
}
