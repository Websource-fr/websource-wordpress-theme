<?php
/**
 * Public template functions of Websource Theme (usable in a child theme or a plugin).
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

use Websource\Theme\Breadcrumb;
use Websource\Theme\Icons;
use Websource\Theme\Settings;

if (!function_exists('websource_theme_get_area')) {
    /**
     * HTML of a theme area: output of the `websource_theme_area_<id>` action followed by the widgets of the area.
     *
     * @param string $id Area id, see Websource\Theme\Theme::areas().
     */
    function websource_theme_get_area(string $id): string
    {
        ob_start();
        /**
         * Fires at the top of a theme area. Dynamic part: area id with dashes turned into underscores
         * (websource_theme_area_footer_before, websource_theme_area_header_search…).
         */
        do_action('websource_theme_area_' . str_replace('-', '_', $id));
        if (is_active_sidebar('wsx-' . $id)) {
            dynamic_sidebar('wsx-' . $id);
        }

        return trim((string) ob_get_clean());
    }
}

if (!function_exists('websource_theme_area')) {
    /** Prints an area between $before and $after, or nothing when it is empty. Returns whether something was printed. */
    function websource_theme_area(string $id, string $before = '', string $after = ''): bool
    {
        $html = websource_theme_get_area($id);
        if ($html === '') {
            return false;
        }
        echo $before . $html . $after; // phpcs:ignore WordPress.Security.EscapeOutput -- widgets output, wrappers are theme markup.

        return true;
    }
}

if (!function_exists('websource_theme_icon')) {
    /** Inline SVG icon (24 px, stroke = currentColor). */
    function websource_theme_icon(string $name, int $size = 24): string
    {
        return Icons::get($name, $size);
    }
}

if (!function_exists('websource_theme_breadcrumb')) {
    function websource_theme_breadcrumb(): void
    {
        Breadcrumb::render();
    }
}

if (!function_exists('websource_theme_cart_count')) {
    function websource_theme_cart_count(): int
    {
        return (function_exists('WC') && WC()->cart) ? (int) WC()->cart->get_cart_contents_count() : 0;
    }
}

if (!function_exists('websource_theme_cart_badge')) {
    /** Cart counter badge, refreshed by WooCommerce fragments and by assets/js/theme.js. */
    function websource_theme_cart_badge(): string
    {
        $count = websource_theme_cart_count();

        return '<span class="wsx-tabbar__badge wsx-cart-count" data-wsx-cart-count' . ($count ? '' : ' hidden') . '>' . (int) $count . '</span>';
    }
}

if (!function_exists('websource_theme_account_url')) {
    function websource_theme_account_url(): string
    {
        if (function_exists('wc_get_page_permalink')) {
            return (string) wc_get_page_permalink('myaccount');
        }

        return is_user_logged_in() ? admin_url('profile.php') : wp_login_url();
    }
}

if (!function_exists('websource_theme_cart_url')) {
    function websource_theme_cart_url(): string
    {
        return function_exists('wc_get_cart_url') ? (string) wc_get_cart_url() : home_url('/');
    }
}

if (!function_exists('websource_theme_logo')) {
    /** Custom logo, or the site name as a text logo. */
    function websource_theme_logo(): string
    {
        if (has_custom_logo()) {
            return (string) get_custom_logo();
        }

        return '<a href="' . esc_url(home_url('/')) . '" class="wsx-logo-text" rel="home">' . esc_html(get_bloginfo('name')) . '</a>';
    }
}

if (!function_exists('websource_theme_search_form')) {
    /** Header search field: products when WooCommerce is active, the whole site otherwise. */
    function websource_theme_search_form(string $idSuffix = 'header'): string
    {
        $html = (string) get_search_form(['echo' => false, 'websource_theme_id' => $idSuffix]);

        /** @param string $html Search form markup. */
        return (string) apply_filters('websource_theme_search_form', $html, $idSuffix);
    }
}

if (!function_exists('websource_theme_header_account')) {
    /** Account link of the header: 'desktop' (icon and label, header actions) or 'mobile' (round icon next to the logo). */
    function websource_theme_header_account(string $context = 'desktop'): string
    {
        $label = is_user_logged_in() ? __('Account', 'websource') : __('Sign in', 'websource');
        if ($context === 'mobile') {
            $html = '<a class="wsx-hicon" href="' . esc_url(websource_theme_account_url()) . '" aria-label="' . esc_attr($label) . '">' . websource_theme_icon('account') . '</a>';
        } else {
            $html = '<a class="wsx-hact" href="' . esc_url(websource_theme_account_url()) . '">' . websource_theme_icon('account', 22) . '<span>' . esc_html($label) . '</span></a>';
        }

        /**
         * @param string $html    Account link of the header. An account block extension returns its own markup
         *                        (one single root element); an empty string removes the link.
         * @param string $context 'desktop' or 'mobile'.
         */
        return (string) apply_filters('websource_theme_header_account', $html, $context);
    }
}

if (!function_exists('websource_theme_header_cart')) {
    /** Cart link of the header (empty without WooCommerce): 'desktop' or 'mobile', see websource_theme_header_account(). */
    function websource_theme_header_cart(string $context = 'desktop'): string
    {
        if (!class_exists('WooCommerce')) {
            return '';
        }
        if ($context === 'mobile') {
            $html = '<a class="wsx-hicon" href="' . esc_url(websource_theme_cart_url()) . '" aria-label="' . esc_attr__('Cart', 'websource') . '">'
                . '<span class="wsx-tabbar__icon">' . websource_theme_icon('cart') . websource_theme_cart_badge() . '</span></a>';
        } else {
            $html = '<a class="wsx-hact wsx-hact--cart" href="' . esc_url(websource_theme_cart_url()) . '">'
                . '<span class="wsx-tabbar__icon">' . websource_theme_icon('cart', 22) . websource_theme_cart_badge() . '</span>'
                . '<span>' . esc_html__('Cart', 'websource') . '</span></a>';
        }

        /**
         * @param string $html    Cart link of the header. A mini-cart extension returns its own markup (one single
         *                        root element); an empty string removes the link.
         * @param string $context 'desktop' or 'mobile'.
         */
        return (string) apply_filters('websource_theme_header_cart', $html, $context);
    }
}

if (!function_exists('websource_theme_pagination')) {
    function websource_theme_pagination(): void
    {
        $links = paginate_links([
            'type' => 'array',
            'mid_size' => 1,
            'prev_text' => '<span class="wsx-flip" aria-hidden="true">‹</span><span class="sr-only">' . esc_html__('Previous', 'websource') . '</span>',
            'next_text' => '<span class="sr-only">' . esc_html__('Next', 'websource') . '</span><span class="wsx-flip" aria-hidden="true">›</span>',
        ]);
        if (!$links) {
            return;
        }
        echo '<nav class="wsx-pagination" aria-label="' . esc_attr__('Pagination', 'websource') . '"><ul class="page-numbers">';
        foreach ($links as $link) {
            echo '<li>' . $link . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput -- paginate_links() output.
        }
        echo '</ul></nav>';
    }
}

if (!function_exists('websource_theme_setting')) {
    function websource_theme_setting(string $key): string
    {
        return Settings::get($key);
    }
}

if (!function_exists('websource_theme_posted_on')) {
    function websource_theme_posted_on(): string
    {
        return '<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time>';
    }
}
