<?php

namespace Websource\Theme;

defined('ABSPATH') || exit;

/**
 * Design tokens (CSS variables). Same names as the PrestaShop theme (`--color-brand-*`, `--color-ink`,
 * `--radius-xl`, `--wst-card-shadow`…) plus short `--wst-*` aliases.
 *
 * The compiled CSS already contains the defaults: only the values changed through the
 * `websource_theme_tokens` filter (or the Customizer colour) are printed in the page.
 */
final class Tokens
{
    /** Short alias => canonical variable. */
    const ALIASES = [
        '--wst-brand' => '--color-brand-600',
        '--wst-brand-light' => '--color-brand-500',
        '--wst-brand-dark' => '--color-brand-700',
        '--wst-brand-soft' => '--color-brand-50',
        '--wst-brand-on' => '--color-brand-on',
        '--wst-secondary' => '--color-secondary',
        '--wst-accent' => '--color-accent',
        '--wst-highlight' => '--color-accent-wst',
        '--wst-ink' => '--color-ink',
        '--wst-line' => '--color-line',
        '--wst-page' => '--color-page',
        '--wst-surface' => '--color-surface',
        '--wst-footer' => '--color-footer',
        '--wst-footer-ink' => '--color-footer-ink',
        '--wst-radius' => '--radius-xl',
        '--wst-radius-lg' => '--radius-2xl',
        '--wst-font' => '--font-sans',
        '--wst-font-head' => '--font-head',
        '--wst-container' => '--container-page',
    ];

    public static function boot(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue'], 20);
        add_action('enqueue_block_editor_assets', [self::class, 'enqueueEditor']);
    }

    /** Default values: must stay in sync with assets/src/tokens.css. */
    public static function defaults(): array
    {
        $sans = 'ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';

        return [
            '--font-sans' => $sans,
            '--font-head' => 'var(--font-sans)',
            '--color-brand-50' => '#effaf8',
            '--color-brand-100' => '#d6f0ec',
            '--color-brand-200' => '#b0e0d9',
            '--color-brand-300' => '#7fcbc1',
            '--color-brand-400' => '#4bb0a4',
            '--color-brand-500' => '#1a9b8f',
            '--color-brand-600' => '#0f766e',
            '--color-brand-700' => '#0b5f58',
            '--color-brand-800' => '#084a45',
            '--color-brand-900' => '#05332f',
            '--color-brand' => '#0f766e',
            '--color-brand-fill' => '#0f766e',
            '--color-brand-on' => '#ffffff',
            '--color-brand-text' => '#0b5f58',
            '--color-secondary' => '#0b3a5c',
            '--color-secondary-fill' => '#0b3a5c',
            '--color-secondary-on' => '#ffffff',
            '--color-secondary-text' => '#0b3a5c',
            '--color-accent' => '#f5b83d',
            '--color-accent-fill' => '#f5b83d',
            '--color-accent-on' => '#111827',
            '--color-accent-text' => '#8a5a00',
            '--color-accent-wst' => '#f0556b',
            '--color-ink' => '#14282b',
            '--color-line' => '#d9e6e4',
            '--color-page' => '#ffffff',
            '--color-surface' => '#ffffff',
            '--color-footer' => '#14282b',
            '--color-footer-ink' => '#f1f5f4',
            '--color-footer-muted' => '#b9c6c4',
            '--color-footer-deep' => '#0d1b1d',
            '--color-footer-line' => '#2b3f42',
            '--color-footer-hover' => '#f7c34c',
            '--container-page' => '80rem',
            '--radius-xl' => '1rem',
            '--radius-2xl' => '1.5rem',
            '--wst-card-shadow' => 'none',
            '--wst-muted' => 'color-mix(in srgb, var(--color-ink) 65%, transparent)',
        ];
    }

    /**
     * Tokens after the `websource_theme_tokens` filter, aliases resolved.
     */
    public static function all(): array
    {
        $tokens = self::defaults();
        $primary = self::sanitizeHex(Settings::get('primary_color'));
        if ($primary !== '') {
            $tokens = array_merge($tokens, self::scale('brand', $primary));
        }

        /**
         * Design tokens printed on :root.
         *
         * @param array<string,string> $tokens   CSS variable => value. Canonical names (`--color-brand-600`…) or
         *                                       `--wst-*` aliases; any extra `--…` variable is printed as is.
         * @param array<string,string> $defaults Built-in defaults.
         */
        $filtered = apply_filters('websource_theme_tokens', $tokens, self::defaults());
        if (!is_array($filtered)) {
            return $tokens;
        }

        // An alias alone drives its canonical variable; `--wst-brand` alone rebuilds the whole brand scale.
        foreach (self::ALIASES as $alias => $canonical) {
            if (!isset($filtered[$alias]) || $filtered[$alias] === '') {
                continue;
            }
            $changed = isset($filtered[$canonical]) && $filtered[$canonical] !== $tokens[$canonical];
            if ($changed) {
                continue;
            }
            if ($alias === '--wst-brand' && self::sanitizeHex($filtered[$alias]) !== '') {
                $filtered = array_merge($filtered, self::scale('brand', self::sanitizeHex($filtered[$alias])));
            } else {
                $filtered[$canonical] = $filtered[$alias];
            }
        }

        $clean = [];
        foreach ($filtered as $name => $value) {
            $name = (string) $name;
            $value = self::sanitizeValue((string) $value);
            if ($value !== '' && preg_match('/^--[a-zA-Z0-9_-]+$/', $name)) {
                $clean[$name] = $value;
            }
        }

        return $clean;
    }

    /** Declarations that differ from the compiled defaults. */
    public static function css(): string
    {
        $defaults = self::defaults();
        $out = '';
        foreach (self::all() as $name => $value) {
            if (isset($defaults[$name]) && $defaults[$name] === $value) {
                continue;
            }
            $out .= $name . ':' . $value . ';';
        }

        return $out === '' ? '' : ':root{' . $out . '}';
    }

    public static function enqueue(): void
    {
        $css = self::css();
        if ($css !== '') {
            wp_add_inline_style('websource-theme', $css);
        }
    }

    public static function enqueueEditor(): void
    {
        $css = self::css();
        if ($css !== '') {
            wp_register_style('websource-theme-tokens', false, [], WEBSOURCE_THEME_VERSION);
            wp_enqueue_style('websource-theme-tokens');
            wp_add_inline_style('websource-theme-tokens', $css);
        }
    }

    /**
     * 50–900 scale of a colour, plus base / fill / on / text variants.
     *
     * @return array<string,string>
     */
    public static function scale(string $name, string $hex): array
    {
        $rgb = self::toRgb($hex);
        $mix = static function (array $a, array $b, float $t): string {
            $c = [];
            for ($i = 0; $i < 3; $i++) {
                $c[] = (int) round($a[$i] + ($b[$i] - $a[$i]) * $t);
            }

            return sprintf('#%02x%02x%02x', $c[0], $c[1], $c[2]);
        };
        $white = [255, 255, 255];
        $black = [0, 0, 0];
        $steps = [
            '50' => $mix($rgb, $white, 0.93), '100' => $mix($rgb, $white, 0.83), '200' => $mix($rgb, $white, 0.67),
            '300' => $mix($rgb, $white, 0.47), '400' => $mix($rgb, $white, 0.25), '500' => $mix($rgb, $white, 0.1),
            '600' => $hex, '700' => $mix($rgb, $black, 0.2), '800' => $mix($rgb, $black, 0.38), '900' => $mix($rgb, $black, 0.57),
        ];
        $vars = [];
        foreach ($steps as $step => $value) {
            $vars['--color-' . $name . '-' . $step] = $value;
        }
        $on = self::contrast($rgb, $white) >= 4.5 ? '#ffffff' : '#111827';
        $text = $hex;
        $t = 0.0;
        while (self::contrast(self::toRgb($text), $white) < 4.8 && $t < 1) {
            $t += 0.05;
            $text = $mix($rgb, $black, $t);
        }
        $vars['--color-' . $name] = $hex;
        $vars['--color-' . $name . '-fill'] = $hex;
        $vars['--color-' . $name . '-on'] = $on;
        $vars['--color-' . $name . '-text'] = $text;

        return $vars;
    }

    public static function sanitizeHex($value): string
    {
        $value = trim((string) $value);
        if (preg_match('/^#([0-9a-fA-F]{3})$/', $value, $m)) {
            $h = $m[1];
            $value = '#' . $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
        }

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? strtolower($value) : '';
    }

    /** A CSS value that cannot close the rule or the <style> tag. */
    private static function sanitizeValue(string $value): string
    {
        $value = trim(wp_strip_all_tags($value));

        return preg_match('/[{}<>;\\\\]/', $value) ? '' : $value;
    }

    private static function toRgb(string $hex): array
    {
        return [hexdec(substr($hex, 1, 2)), hexdec(substr($hex, 3, 2)), hexdec(substr($hex, 5, 2))];
    }

    private static function luminance(array $rgb): float
    {
        $l = [];
        foreach ($rgb as $c) {
            $c /= 255;
            $l[] = $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
        }

        return 0.2126 * $l[0] + 0.7152 * $l[1] + 0.0722 * $l[2];
    }

    private static function contrast(array $a, array $b): float
    {
        $la = self::luminance($a);
        $lb = self::luminance($b);

        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }
}
