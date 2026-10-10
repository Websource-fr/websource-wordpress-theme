<?php

namespace Websource\Theme;

defined('ABSPATH') || exit;

/**
 * Appearance > Customize > Theme options: layouts and the main colour.
 * Everything here can also be driven by code (filters `websource_theme_settings` and `websource_theme_tokens`).
 */
final class Customizer
{
    public static function boot(): void
    {
        add_action('customize_register', [self::class, 'register']);
    }

    private static function labels(): array
    {
        return [
            'shop_sidebar' => [__('Shop column', 'websource'), [
                'left' => __('Left column (filters, categories)', 'websource'),
                'none' => __('No column', 'websource'),
            ]],
            'shop_columns' => [__('Products per row', 'websource'), [
                '3' => '3',
                '4' => '4',
            ]],
        ];
    }

    public static function register(\WP_Customize_Manager $wp): void
    {
        $wp->add_section('websource_theme', [
            'title' => __('Theme options', 'websource'),
            'description' => __('Main colour, shop column, products per row and footer texts.', 'websource') . ' <a href="' . esc_url(wp_get_theme()->get('ThemeURI')) . '">' . esc_html__('More layouts in the Premium version', 'websource') . '</a>',
            'priority' => 30,
        ]);

        $defaults = Settings::defaults();

        $wp->add_setting('websource_primary_color', ['default' => '', 'sanitize_callback' => [Tokens::class, 'sanitizeHex']]);
        $wp->add_control(new \WP_Customize_Color_Control($wp, 'websource_primary_color', [
            'label' => __('Main colour', 'websource'),
            'description' => __('Leave empty to keep the Lagoon green (#0f766e). The lighter and darker shades are computed for you.', 'websource'),
            'section' => 'websource_theme',
        ]));

        foreach (self::labels() as $key => $def) {
            $choices = $def[1];
            $wp->add_setting('websource_' . $key, [
                'default' => $defaults[$key],
                'sanitize_callback' => static function ($value) use ($choices, $defaults, $key) {
                    return isset($choices[(string) $value]) ? (string) $value : $defaults[$key];
                },
            ]);
            $wp->add_control('websource_' . $key, ['label' => $def[0], 'section' => 'websource_theme', 'type' => 'select', 'choices' => $choices]);
        }

        $wp->add_setting('websource_footer_text', ['default' => '', 'sanitize_callback' => 'sanitize_textarea_field']);
        $wp->add_control('websource_footer_text', [
            'label' => __('Footer presentation text', 'websource'),
            'description' => __('Shown under the logo, in the first footer column.', 'websource'),
            'section' => 'websource_theme',
            'type' => 'textarea',
        ]);

        $wp->add_setting('websource_copyright', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
        $wp->add_control('websource_copyright', [
            'label' => __('Copyright line', 'websource'),
            'description' => __('Leave empty for “© year site name”.', 'websource'),
            'section' => 'websource_theme',
            'type' => 'text',
        ]);
    }
}
