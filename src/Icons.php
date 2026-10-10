<?php

namespace Websource\Theme;

defined('ABSPATH') || exit;

/**
 * Inline SVG icons (same drawings as the PrestaShop theme): no icon font, no extra request.
 */
final class Icons
{
    const PATHS = [
        'menu' => '<path d="M4 7h16M4 12h16M4 17h10" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>',
        'search' => '<circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.9"/><path d="m16 16 4.2 4.2" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>',
        'account' => '<circle cx="12" cy="8.5" r="3.6" fill="none" stroke="currentColor" stroke-width="1.9"/><path d="M4.8 20c.6-3.6 3.5-5.6 7.2-5.6s6.6 2 7.2 5.6" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>',
        'cart' => '<g fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2.5 3.5h2.7l2.3 11.2a1.5 1.5 0 0 0 1.5 1.2h8.2a1.5 1.5 0 0 0 1.5-1.1L20.5 8H6"/></g>',
        'home' => '<path d="M3 11.5 12 4l9 7.5M5.5 10v9.5h4.5v-5.5h4v5.5h4.5V10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
        'star' => '<path d="m12 3.5 2.4 5 5.4.7-4 3.7 1 5.4L12 15.6 7.2 18.3l1-5.4-4-3.7 5.4-.7L12 3.5Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
        'mail' => '<path d="M4 6.5h16v11H4z M4.5 7l7.5 6 7.5-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/>',
        'close' => '<path d="M6 6l12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>',
        'filter' => '<path d="M4 6h16M7 12h10M10 18h4" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>',
        'orders' => '<path d="M6 3.5h12v17l-3-2-3 2-3-2-3 2zM9 8.5h6M9 12.5h6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
        'downloads' => '<path d="M12 4v11m0 0-4-4m4 4 4-4M5 19.5h14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
        'address' => '<path d="M12 21s7-6.2 7-11.5a7 7 0 1 0-14 0C5 14.8 12 21 12 21Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="9.5" r="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>',
        'payment' => '<rect x="3" y="5.5" width="18" height="13" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M3 10h18M7 15h4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
        'logout' => '<path d="M10 4.5H6.5a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2H10M15 8l4 4-4 4M19 12H9.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
        'dashboard' => '<path d="M4 4.5h7v7H4zM13 4.5h7v4h-7zM13 11.5h7v8h-7zM4 14.5h7v5H4z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
        'check' => '<path d="m5 12.5 4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'chevron' => '<path d="m9 5.5 6.5 6.5L9 18.5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>',
    ];

    public static function get(string $name, int $size = 24): string
    {
        if (!isset(self::PATHS[$name])) {
            return '';
        }

        return '<svg viewBox="0 0 24 24" width="' . (int) $size . '" height="' . (int) $size . '" aria-hidden="true" focusable="false">' . self::PATHS[$name] . '</svg>';
    }
}
