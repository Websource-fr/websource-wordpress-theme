<?php

namespace Websource\Theme;

defined('ABSPATH') || exit;

/**
 * Layout settings (Customizer theme mods), overridable with the `websource_theme_settings` filter.
 */
final class Settings
{
    const CHOICES = [
        'header_desktop' => ['classic'],
        'header_mobile' => ['icons_right'],
        'mobile_nav' => ['top'],
        'footer_style' => ['dark'],
        'product_layout' => ['classic'],
        'product_details' => ['tabs'],
        'shop_sidebar' => ['left', 'none'],
        'shop_columns' => ['3', '4'],
    ];

    /** @var array|null */
    private static $cache;

    public static function defaults(): array
    {
        return [
            'header_desktop' => 'classic',
            'header_mobile' => 'icons_right',
            'mobile_nav' => 'top',
            'footer_style' => 'dark',
            'product_layout' => 'classic',
            'product_details' => 'tabs',
            'shop_sidebar' => 'left',
            'shop_columns' => '3',
            'primary_color' => '',
            'footer_text' => '',
            'copyright' => '',
        ];
    }

    public static function all(): array
    {
        if (self::$cache !== null && !is_customize_preview()) {
            return self::$cache;
        }
        $settings = [];
        foreach (self::defaults() as $key => $default) {
            $settings[$key] = (string) get_theme_mod('websource_' . $key, $default);
        }
        /**
         * Layout settings of the theme.
         *
         * @param array $settings header_desktop, header_mobile, mobile_nav, footer_style, product_layout,
         *                        product_details, shop_sidebar, shop_columns, primary_color, footer_text, copyright.
         */
        $settings = (array) apply_filters('websource_theme_settings', $settings);
        foreach (self::CHOICES as $key => $allowed) {
            if (!isset($settings[$key]) || !in_array((string) $settings[$key], $allowed, true)) {
                $settings[$key] = self::defaults()[$key];
            }
        }
        $settings += self::defaults();

        return self::$cache = $settings;
    }

    public static function get(string $key): string
    {
        $all = self::all();

        return isset($all[$key]) ? (string) $all[$key] : '';
    }
}
