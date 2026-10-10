<?php

namespace Websource\Theme;

defined('ABSPATH') || exit;

/**
 * WooCommerce integration: wrappers, product cards, product page zones, cart counter.
 * Nothing runs when WooCommerce is not active.
 */
final class Shop
{
    /** Blocks of the product page: block => [hook, first priority, last priority]. */
    const SUMMARY_BLOCKS = [
        'title' => [5, 5],
        'price' => [10, 10],
        'short_description' => [20, 20],
        'buy' => [30, 30],
        'meta' => [40, 50],
    ];

    public static function boot(): void
    {
        add_action('after_setup_theme', [self::class, 'init'], 20);
    }

    public static function init(): void
    {
        if (!class_exists('WooCommerce')) {
            return;
        }

        // Page wrappers and breadcrumb come from the theme layout.
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
        add_action('woocommerce_before_main_content', [self::class, 'wrapperStart'], 10);
        add_action('woocommerce_after_main_content', [self::class, 'wrapperEnd'], 10);

        // Product list: toolbar (count + sorting), columns.
        add_action('woocommerce_before_shop_loop', [self::class, 'toolbarStart'], 15);
        add_action('woocommerce_before_shop_loop', [self::class, 'toolbarEnd'], 35);
        add_filter('loop_shop_columns', [self::class, 'columns']);
        add_filter('loop_shop_per_page', [self::class, 'perPage']);
        add_filter('woocommerce_output_related_products_args', [self::class, 'relatedArgs']);
        add_filter('woocommerce_upsell_display_args', [self::class, 'relatedArgs']);
        add_filter('woocommerce_cross_sells_columns', static function () {
            return 4;
        });
        add_filter('woocommerce_pagination_args', [self::class, 'paginationArgs']);

        // Product card: link around the image only, linked title, badges.
        remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
        remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
        add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 5);
        add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 15);
        add_action('woocommerce_before_shop_loop_item_title', [self::class, 'flags'], 20);
        add_action('woocommerce_shop_loop_item_title', [self::class, 'loopTitle'], 10);
        add_filter('woocommerce_loop_add_to_cart_args', [self::class, 'loopButtonArgs']);
        add_filter('woocommerce_product_loop_title_classes', static function () {
            return 'wsx-pm__title';
        });

        // Product page: badges in the gallery zone, blocks wrapped for the layouts.
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
        add_action('woocommerce_before_single_product_summary', [self::class, 'singleFlags'], 10);
        foreach (self::SUMMARY_BLOCKS as $block => $range) {
            add_action('woocommerce_single_product_summary', static function () use ($block) {
                echo '<div class="wsx-pb wsx-pb--' . esc_attr(str_replace('_', '-', $block)) . '" data-wsx-block="' . esc_attr($block) . '">';
            }, $range[0] - 1);
            add_action('woocommerce_single_product_summary', static function () {
                echo '</div>';
            }, $range[1] + 1);
        }
        foreach ([['woocommerce_before_single_product_summary', 'gallery', 19, 21], ['woocommerce_after_single_product_summary', 'details', 9, 11], ['woocommerce_after_single_product_summary', 'related', 14, 21]] as $wrap) {
            add_action($wrap[0], static function () use ($wrap) {
                echo '<div class="wsx-pb wsx-pb--' . esc_attr($wrap[1]) . '" data-wsx-block="' . esc_attr($wrap[1]) . '">';
            }, $wrap[2]);
            add_action($wrap[0], static function () {
                echo '</div>';
            }, $wrap[3]);
        }
        // The panel titles are printed by the theme (tabs): no second heading inside.
        add_filter('woocommerce_product_description_heading', '__return_empty_string');
        add_filter('woocommerce_product_additional_information_heading', '__return_empty_string');
        add_action('woocommerce_single_product_summary', [self::class, 'reassurance'], 35);
        add_action('woocommerce_after_single_product_summary', [self::class, 'productFooter'], 30);
        add_action('wp', [self::class, 'hideBlocks']);

        // Quantity field with − / + buttons (handled by assets/js/theme.js).
        add_action('woocommerce_before_quantity_input_field', [self::class, 'qtyMinus']);
        add_action('woocommerce_after_quantity_input_field', [self::class, 'qtyPlus']);

        // Cart counter in the header and in the mobile bar.
        add_filter('woocommerce_add_to_cart_fragments', [self::class, 'fragments']);

        add_action('woocommerce_after_cart', [self::class, 'cartFooter'], 20);
        add_filter('woocommerce_account_menu_item_classes', [self::class, 'accountItemClasses'], 10, 2);
    }

    /** Layout of the product page: preset, details mode, hidden blocks. */
    public static function productLayout(): array
    {
        $layout = [
            'preset' => Settings::get('product_layout'),
            'details_mode' => Settings::get('product_details'),
            'hidden' => [],
        ];
        /**
         * Product page layout.
         *
         * @param array $layout preset (classic), details_mode (tabs),
         *                      hidden (blocks to hide among flags, gallery, title, price, short_description, buy, meta,
         *                      reassurance, details, related).
         */
        $filtered = (array) apply_filters('websource_theme_product_layout', $layout, get_queried_object_id());
        if (!isset($filtered['preset']) || !in_array($filtered['preset'], Settings::CHOICES['product_layout'], true)) {
            $filtered['preset'] = $layout['preset'];
        }
        if (!isset($filtered['details_mode']) || !in_array($filtered['details_mode'], Settings::CHOICES['product_details'], true)) {
            $filtered['details_mode'] = $layout['details_mode'];
        }
        $filtered['hidden'] = isset($filtered['hidden']) && is_array($filtered['hidden']) ? array_map('strval', $filtered['hidden']) : [];

        return $filtered;
    }

    /** Hidden blocks are not rendered at all (their hooks do not run). */
    public static function hideBlocks(): void
    {
        if (!is_product()) {
            return;
        }
        $hidden = self::productLayout()['hidden'];
        $map = [
            'title' => ['woocommerce_single_product_summary', 'woocommerce_template_single_title', 5],
            'price' => ['woocommerce_single_product_summary', 'woocommerce_template_single_price', 10],
            'short_description' => ['woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20],
            'buy' => ['woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30],
            'meta' => ['woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40],
            'gallery' => ['woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20],
            'details' => ['woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10],
            'related' => ['woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20],
        ];
        foreach ($hidden as $block) {
            if (isset($map[$block])) {
                remove_action($map[$block][0], $map[$block][1], $map[$block][2]);
            }
            if ($block === 'price') {
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
            }
            if ($block === 'meta') {
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
            }
            if ($block === 'related') {
                remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
            }
            if ($block === 'flags') {
                remove_action('woocommerce_before_single_product_summary', [self::class, 'singleFlags'], 10);
            }
            if ($block === 'reassurance') {
                remove_action('woocommerce_single_product_summary', [self::class, 'reassurance'], 35);
            }
        }
    }

    public static function hasSidebar(): bool
    {
        if (is_product() || Settings::get('shop_sidebar') !== 'left') {
            return false;
        }

        /**
         * Whether the shop column (left column, "Filter" button of the toolbar) is shown on this page. A filter
         * extension that brings its own column and its own button returns false.
         *
         * @param bool $show
         */
        return (bool) apply_filters('websource_theme_shop_sidebar', true);
    }

    public static function wrapperStart(): void
    {
        echo '<div class="wsx-container">';
        websource_theme_breadcrumb();
        if (self::hasSidebar()) {
            echo '<div class="wsx-cols wsx-cols--left">';
            get_template_part('template-parts/shop-sidebar');
            echo '<div id="content-wrapper" class="wsx-cols__main min-w-0">';
        } else {
            echo '<div id="content-wrapper" class="min-w-0">';
        }
        echo '<div id="content">';
    }

    public static function wrapperEnd(): void
    {
        echo '</div></div>';
        if (self::hasSidebar()) {
            echo '</div>';
        }
        echo '</div>';
    }

    public static function toolbarStart(): void
    {
        echo '<div class="wsx-toolbar">';
        if (self::hasSidebar()) {
            echo '<button type="button" class="wsx-btn wsx-btn--ghost wsx-toolbar__filter" data-wsx-drawer-open="left-column" aria-controls="left-column" aria-expanded="false">'
                . websource_theme_icon('filter', 20) . '<span>' . esc_html__('Filter', 'websource') . '</span></button>';
        }
    }

    public static function toolbarEnd(): void
    {
        echo '</div>';
    }

    public static function columns(): int
    {
        return (int) Settings::get('shop_columns');
    }

    public static function perPage($count): int
    {
        return (int) Settings::get('shop_columns') * 4;
    }

    public static function relatedArgs($args)
    {
        $args['posts_per_page'] = 4;
        $args['columns'] = 4;

        return $args;
    }

    public static function paginationArgs($args)
    {
        $args['prev_text'] = '<span class="wsx-flip" aria-hidden="true">‹</span><span class="sr-only">' . esc_html__('Previous', 'websource') . '</span>';
        $args['next_text'] = '<span class="sr-only">' . esc_html__('Next', 'websource') . '</span><span class="wsx-flip" aria-hidden="true">›</span>';
        $args['mid_size'] = 1;

        return $args;
    }

    public static function loopTitle(): void
    {
        global $product;
        $tag = is_front_page() ? 'h3' : 'h2';
        $link = apply_filters('woocommerce_loop_product_link', get_the_permalink(), $product);
        echo '<' . $tag . ' class="' . esc_attr(apply_filters('woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title')) . '"><a href="' . esc_url($link) . '">' . esc_html(get_the_title()) . '</a></' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
    }

    public static function loopButtonArgs($args)
    {
        $args['class'] = trim((isset($args['class']) ? $args['class'] : '') . ' wsx-btn wsx-btn--soft wsx-pm__btn');

        return $args;
    }

    /**
     * Badges of a product: sale (with the percentage), new, out of stock.
     *
     * @return array<int, array{type:string,label:string}>
     */
    public static function productFlags(\WC_Product $product): array
    {
        $flags = [];
        if (!$product->is_in_stock()) {
            $flags[] = ['type' => 'out_of_stock', 'label' => __('Out of stock', 'websource')];
        }
        if ($product->is_on_sale()) {
            $label = __('Sale', 'websource');
            $regular = (float) $product->get_regular_price();
            $sale = (float) $product->get_sale_price();
            if ($product->is_type('variable')) {
                $regular = (float) $product->get_variation_regular_price('max');
                $sale = (float) $product->get_variation_sale_price('min');
            }
            if ($regular > 0 && $sale > 0 && $sale < $regular) {
                $label = '−' . round(100 - ($sale / $regular * 100)) . ' %';
            }
            $flags[] = ['type' => 'discount', 'label' => $label];
        }
        /** @param int $days A product is “new” during this number of days after its publication (0 = never). */
        $days = (int) apply_filters('websource_theme_new_days', 20);
        $created = $product->get_date_created();
        if ($days > 0 && $created && (time() - $created->getTimestamp()) < $days * DAY_IN_SECONDS) {
            $flags[] = ['type' => 'new', 'label' => __('New', 'websource')];
        }

        /** @param array $flags Each flag: type (class suffix) and label. */
        return (array) apply_filters('websource_theme_product_flags', $flags, $product);
    }

    public static function flags(): void
    {
        global $product;
        if (!$product instanceof \WC_Product) {
            return;
        }
        $flags = self::productFlags($product);
        if (!$flags) {
            return;
        }
        echo '<ul class="wsx-flags product-flags">';
        foreach ($flags as $flag) {
            echo '<li class="wsx-flag product-flag ' . esc_attr($flag['type']) . '">' . esc_html($flag['label']) . '</li>';
        }
        echo '</ul>';
    }

    public static function singleFlags(): void
    {
        echo '<div class="wsx-pb wsx-pb--flags" data-wsx-block="flags">';
        self::flags();
        echo '</div>';
    }

    public static function reassurance(): void
    {
        websource_theme_area('product-reassurance', '<div class="wsx-pb wsx-pb--reassurance" data-wsx-block="reassurance">', '</div>');
    }

    public static function productFooter(): void
    {
        websource_theme_area('product-footer', '<div class="wsx-product-footer">', '</div>');
    }

    public static function cartFooter(): void
    {
        websource_theme_area('cart-footer', '<div class="wsx-cart-footer">', '</div>');
    }

    public static function qtyMinus(): void
    {
        echo '<button type="button" class="wsx-qty__btn" data-wsx-qty="-1" aria-label="' . esc_attr__('Decrease quantity', 'websource') . '">−</button>';
    }

    public static function qtyPlus(): void
    {
        echo '<button type="button" class="wsx-qty__btn" data-wsx-qty="1" aria-label="' . esc_attr__('Increase quantity', 'websource') . '">+</button>';
    }

    public static function fragments($fragments)
    {
        $fragments['span.wsx-cart-count'] = websource_theme_cart_badge();

        return $fragments;
    }

    public static function accountItemClasses($classes, $endpoint)
    {
        $classes[] = 'wsx-account-nav__item';

        return $classes;
    }

    /** Icon name for an account menu endpoint. */
    public static function accountIcon(string $endpoint): string
    {
        $map = [
            'dashboard' => 'dashboard', 'orders' => 'orders', 'downloads' => 'downloads', 'edit-address' => 'address',
            'payment-methods' => 'payment', 'edit-account' => 'account', 'customer-logout' => 'logout',
        ];

        return isset($map[$endpoint]) ? $map[$endpoint] : 'chevron';
    }
}
