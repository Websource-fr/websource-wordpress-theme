<?php

namespace Websource\Theme;

defined('ABSPATH') || exit;

/**
 * Block editor additions: block styles and block patterns (presentation only).
 */
final class Editor
{
    public static function boot(): void
    {
        add_action('init', [self::class, 'register']);
    }

    public static function register(): void
    {
        register_block_style('core/button', ['name' => 'websource-soft', 'label' => __('Soft', 'websource')]);
        register_block_style('core/group', ['name' => 'websource-card', 'label' => __('Card', 'websource')]);
        register_block_style('core/list', ['name' => 'websource-check', 'label' => __('Check marks', 'websource')]);

        register_block_pattern_category('websource', ['label' => __('Websource', 'websource')]);

        register_block_pattern('websource/call-to-action', [
            'title' => __('Call to action', 'websource'),
            'description' => __('A card with a heading, a sentence and a button.', 'websource'),
            'categories' => ['websource', 'call-to-action'],
            'content' => '<!-- wp:group {"className":"is-style-websource-card","layout":{"type":"constrained"}} -->'
                . '<div class="wp-block-group is-style-websource-card">'
                . '<!-- wp:heading {"textAlign":"center"} --><h2 class="wp-block-heading has-text-align-center">' . esc_html__('Ready to get started?', 'websource') . '</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">' . esc_html__('Describe your offer in one sentence, then invite your visitors to take the next step.', 'websource') . '</p><!-- /wp:paragraph -->'
                . '<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} --><div class="wp-block-buttons">'
                . '<!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button">' . esc_html__('Contact us', 'websource') . '</a></div><!-- /wp:button -->'
                . '</div><!-- /wp:buttons -->'
                . '</div><!-- /wp:group -->',
        ]);

        $columns = '';
        foreach ([
            [__('Fast delivery', 'websource'), __('Explain how and when the order is shipped.', 'websource')],
            [__('Secure payment', 'websource'), __('List the payment methods you accept.', 'websource')],
            [__('Customer service', 'websource'), __('Say how and when you can be reached.', 'websource')],
        ] as $column) {
            $columns .= '<!-- wp:column --><div class="wp-block-column">'
                . '<!-- wp:group {"className":"is-style-websource-card"} --><div class="wp-block-group is-style-websource-card">'
                . '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html($column[0]) . '</h3><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>' . esc_html($column[1]) . '</p><!-- /wp:paragraph -->'
                . '</div><!-- /wp:group -->'
                . '</div><!-- /wp:column -->';
        }
        register_block_pattern('websource/three-features', [
            'title' => __('Three features', 'websource'),
            'description' => __('Three cards side by side, to present your commitments.', 'websource'),
            'categories' => ['websource', 'featured'],
            'content' => '<!-- wp:columns --><div class="wp-block-columns">' . $columns . '</div><!-- /wp:columns -->',
        ]);
    }
}
