<?php
/**
 * Customer account dashboard: greeting, then one card per account section (as on the PrestaShop “my account” page).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined('ABSPATH') || exit;

$allowed_html = ['a' => ['href' => []]];
?>
<p class="wsx-account-hello">
    <?php
    printf(
        /* translators: 1: user display name 2: logout url */
        wp_kses(__('Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'websource'), $allowed_html), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
        '<strong>' . esc_html($current_user->display_name) . '</strong>',
        esc_url(wc_logout_url())
    );
    ?>
</p>

<ul class="wsx-account-links">
    <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
        <?php
        if ($endpoint === 'dashboard') {
            continue;
        }
        ?>
    <li>
      <a class="wsx-card wsx-account-links__item" href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>">
        <span class="wsx-account-links__icon"><?php echo websource_theme_icon(\Websource\Theme\Shop::accountIcon($endpoint), 28); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <span class="wsx-account-links__label"><?php echo esc_html($label); ?></span>
      </a>
    </li>
    <?php endforeach; ?>
</ul>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action('woocommerce_account_dashboard');

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_before_my_account');

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_after_my_account');
