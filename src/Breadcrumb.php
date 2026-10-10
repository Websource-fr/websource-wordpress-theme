<?php

namespace Websource\Theme;

defined('ABSPATH') || exit;

/**
 * Breadcrumb trail shared by the visible breadcrumb and the BreadcrumbList JSON-LD.
 */
final class Breadcrumb
{
    /** @var array|null */
    private static $cache;

    /**
     * @return array<int, array{title:string,url:string}>
     */
    public static function links(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $links = [['title' => __('Home', 'websource'), 'url' => home_url('/')]];
        $woo = class_exists('WooCommerce');

        if (is_front_page()) {
            // Home only.
        } elseif ($woo && (is_woocommerce() || is_cart() || is_checkout() || is_account_page())) {
            $crumbs = (new \WC_Breadcrumb())->generate();
            if (is_woocommerce() && !is_shop() && wc_get_page_id('shop') > 0 && (int) get_option('page_on_front') !== wc_get_page_id('shop')) {
                $links[] = ['title' => get_the_title(wc_get_page_id('shop')), 'url' => get_permalink(wc_get_page_id('shop'))];
            }
            foreach ($crumbs as $crumb) {
                $links[] = ['title' => wp_strip_all_tags($crumb[0]), 'url' => (string) $crumb[1]];
            }
        } elseif (is_home()) {
            $links[] = ['title' => get_the_title((int) get_option('page_for_posts')) ?: __('Blog', 'websource'), 'url' => ''];
        } elseif (is_singular('post')) {
            $blog = (int) get_option('page_for_posts');
            if ($blog) {
                $links[] = ['title' => get_the_title($blog), 'url' => get_permalink($blog)];
            }
            $cats = get_the_category();
            if ($cats) {
                $links[] = ['title' => $cats[0]->name, 'url' => get_category_link($cats[0])];
            }
            $links[] = ['title' => get_the_title(), 'url' => ''];
        } elseif (is_page() || is_singular()) {
            foreach (array_reverse(get_post_ancestors(get_queried_object_id())) as $ancestor) {
                $links[] = ['title' => get_the_title($ancestor), 'url' => get_permalink($ancestor)];
            }
            $links[] = ['title' => get_the_title(), 'url' => ''];
        } elseif (is_search()) {
            /* translators: %s: search terms */
            $links[] = ['title' => sprintf(__('Search results for “%s”', 'websource'), get_search_query()), 'url' => ''];
        } elseif (is_404()) {
            $links[] = ['title' => __('Page not found', 'websource'), 'url' => ''];
        } elseif (is_archive()) {
            $links[] = ['title' => wp_strip_all_tags(get_the_archive_title()), 'url' => ''];
        }

        // The last link is the current page: its URL is given to the `websource_theme_breadcrumb` filter.
        $last = count($links) - 1;
        if ($last > 0 && $links[$last]['url'] === '') {
            $links[$last]['url'] = self::currentUrl();
        }

        /**
         * Breadcrumb links, home first, current page last.
         *
         * @param array<int, array{title:string,url:string}> $links
         */
        $links = array_values(array_filter((array) apply_filters('websource_theme_breadcrumb', $links), static function ($l) {
            return is_array($l) && isset($l['title']) && $l['title'] !== '';
        }));

        return self::$cache = $links;
    }

    public static function render(): void
    {
        if (is_front_page()) {
            return;
        }
        $links = self::links();
        if (count($links) < 2) {
            return;
        }
        $last = count($links) - 1;
        echo '<nav aria-label="' . esc_attr__('Breadcrumb', 'websource') . '" class="wsx-breadcrumb my-4 text-sm text-ink/60">';
        echo '<ol class="flex flex-wrap items-center gap-x-2 gap-y-1 list-none p-0 m-0">';
        foreach ($links as $i => $link) {
            echo '<li class="flex items-center gap-2">';
            if ($i < $last && !empty($link['url'])) {
                echo '<a href="' . esc_url($link['url']) . '" class="hover:text-brand-700"><span>' . esc_html($link['title']) . '</span></a><span aria-hidden="true">/</span>';
            } elseif ($i < $last) {
                echo '<span>' . esc_html($link['title']) . '</span><span aria-hidden="true">/</span>';
            } else {
                echo '<span aria-current="page" class="text-ink">' . esc_html($link['title']) . '</span>';
            }
            echo '</li>';
        }
        echo '</ol></nav>';
    }

    /** URL of the page being displayed. */
    private static function currentUrl(): string
    {
        global $wp;
        if (get_option('permalink_structure')) {
            $url = trailingslashit(home_url($wp->request ?? ''));
            if (preg_match('/\.[a-z0-9]{2,5}$/i', (string) ($wp->request ?? ''))) {
                $url = untrailingslashit($url);
            }
        } else {
            $url = home_url('/');
            $keep = array_intersect_key($wp->query_vars ?? [], array_flip(['p', 'page_id', 'cat', 'tag', 'paged', 'post_type', 'product', 'product_cat', 's', 'author', 'm']));
            if ($keep) {
                $url = add_query_arg(array_map('rawurlencode', array_map('strval', $keep)), $url);
            }
        }
        if (is_search() && get_option('permalink_structure')) {
            $url = get_search_link();
            if (get_query_var('post_type') === 'product') {
                $url = add_query_arg(['s' => rawurlencode(get_search_query(false)), 'post_type' => 'product'], home_url('/'));
            }
            if (get_query_var('paged') > 1) {
                $url = add_query_arg('paged', (int) get_query_var('paged'), $url);
            }
        }

        return $url;
    }
}
