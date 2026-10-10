<?php
/**
 * Websource Theme — bootstrap (kept thin: everything lives in src/).
 *
 * @package Websource\Theme
 * @license GPL-2.0-or-later
 */

defined('ABSPATH') || exit;

define('WEBSOURCE_THEME_VERSION', '1.0.5');
define('WEBSOURCE_THEME_DIR', __DIR__);

spl_autoload_register(static function ($class) {
    $prefix = 'Websource\\Theme\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require __DIR__ . '/src/template-tags.php';

\Websource\Theme\Theme::boot();
