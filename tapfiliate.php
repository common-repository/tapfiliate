<?php

declare(strict_types=1);

/**
 * Plugin Name: Tapfiliate
 * Plugin URI: https://wordpress.org/plugins/tapfiliate/
 * Description: Easily integrate the Tapfiliate tracking code.
 * Author: Tapfiliate
 * Author URI: https://tapfiliate.com/
 * Version: 3.2.1
 * Requires at least: 4.4
 * Tested up to: 6.3
 * WC requires at least: 2.6
 * WC tested up to: 4.01
 * Text Domain: tapfiliate
 * License: MIT License
 * License URI: https://opensource.org/licenses/MIT.
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!defined('WP_CONTENT_URL')) {
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
}
if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}
if (!defined('WP_PLUGIN_URL')) {
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
}
if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
}
if (!defined('TAPFILIATE_PLUGIN_VERSION')) {
    define('TAPFILIATE_PLUGIN_VERSION', '3.2.1');
}

define('TAPFILIATE_PLUGIN_PATH', plugin_dir_path(__FILE__));
include(TAPFILIATE_PLUGIN_PATH . 'helpers.php');
include(TAPFILIATE_PLUGIN_PATH . 'snippet/generate-inline-code.php');
include(TAPFILIATE_PLUGIN_PATH . 'woocommerce/functions.php');
include(TAPFILIATE_PLUGIN_PATH . 'woocommerce/admin.php');
include(TAPFILIATE_PLUGIN_PATH . 'woocommerce/tracking-code.php');
include(TAPFILIATE_PLUGIN_PATH . 'woocommerce/actions.php');
include(TAPFILIATE_PLUGIN_PATH . 'wordpress/admin.php');
include(TAPFILIATE_PLUGIN_PATH . 'wordpress/tracking-code.php');
include(TAPFILIATE_PLUGIN_PATH . 'wp-easy-cart/tracking-code.php');

add_action('before_woocommerce_init', function () {
    if (class_exists(FeaturesUtil::class)) {
        FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

function activate_tapfiliate(): void
{
    if (empty(get_option('tap_account_id'))) {
        add_option('tap_account_id', '1-123abc');
    }

    tapfiliate_version_check();

    do_action('tapfiliate_plugin_activated');
}

function deactivate_tapfiliate(): void
{
    do_action('tapfiliate_plugin_deactivated');
}

function admin_init_tapfiliate(): void
{
    register_setting('tapfiliate', 'tap_account_id');
    register_setting('tapfiliate', 'tap_wc_enabled');
    register_setting('tapfiliate', 'tap_wc_connected');
    register_setting('tapfiliate', 'tap_wc_use_woo_customer_id_for_lifetime');
    register_setting('tapfiliate', 'tap_ec_enabled');
}

function admin_menu_tapfiliate(): void
{
    add_options_page('Tapfiliate', 'Tapfiliate', 'manage_options', 'tapfiliate', 'options_page_tapfiliate');
}

function options_page_tapfiliate(): void
{
    tapfiliate_version_check();

    include(TAPFILIATE_PLUGIN_PATH . 'options.php');
}

function tapfiliate(): void
{
    wp_enqueue_script('tapfiliate-js', 'https://script.tapfiliate.com/tapfiliate.js');

    $woo_enabled = 'on' === get_option('tap_wc_enabled');
    $woo_active = tapfiliate_is_woocommerce_activated();
    $wp_easycart_active = in_array('wp-easycart/wpeasycart.php', apply_filters('active_plugins', get_option('active_plugins')));
    $wp_easycart_page = $_GET['ec_page'] ?? null;

    if ($wp_easycart_active && null !== $wp_easycart_page && 'checkout_success' !== $wp_easycart_page) {
        $script = tapfiliate_generate_inline_code(
            false,
            false,
            null,
            null,
            null,
            array(),
            null,
            array(),
            'wp-easy-cart'
        );
        wp_add_inline_script('tapfiliate-js', $script);
    }

    if ($woo_active && (!is_woocommerce() && !is_cart())) {
        is_checkout();
    }

    if ((!$woo_active || !$woo_enabled) && !$wp_easycart_active) {
        tapfiliate_render_wordpress_code();
    }

    if ($woo_enabled && !$wp_easycart_active) {
        tapfiliate_render_woocommerce_code();
    }
}

function tapfiliate_migrate_2_x_to_3_0(): void
{
    if ($page_title = get_option('thank_you_page')) {
        $page = get_page_by_title($page_title);

        $optionQueryParamExternalId = ($query_parameter_external_id = get_option('query_parameter_external_id')) ? " external_id_query_param={$query_parameter_external_id}" : '';
        $optionQueryParamConversionAmount = ($query_parameter_conversion_amount = get_option('query_parameter_conversion_amount')) ? " external_id_query_param={$query_parameter_conversion_amount}" : '';

        $shortcode = trim(
            '<!-- wp:shortcode -->
            [tapfiliate' . $optionQueryParamExternalId . $optionQueryParamConversionAmount . ']
            <!-- /wp:shortcode -->'
        );

        $updatedPage = array(
            'ID'           => $page->ID,
            'post_content' => $shortcode . $page->post_content,
            'post_title'   => $page->post_title,
        );

        wp_update_post($updatedPage);
        delete_option('thank_you_page');
        delete_option('query_parameter_external_id');
        delete_option('query_parameter_conversion_amount');
    }

    $integrate_for = get_option('integrate_for');

    switch ($integrate_for) {
        case 'wc':
            update_option('tap_wc_enabled', true);
            break;

        case 'ec':
            update_option('tap_ec_enabled', true);
            break;
    }
}

function tapfiliate_version_check(): void
{
    $persistedVersion = get_option('tap_plugin_version');
    if (false === $persistedVersion || $persistedVersion !== TAPFILIATE_PLUGIN_VERSION) {
        update_option('tap_plugin_version', TAPFILIATE_PLUGIN_VERSION);
    }

    if (version_compare(get_option('tap_plugin_version'), '3.0.0', '<')) {
        tapfiliate_migrate_2_x_to_3_0();
    }
}

register_activation_hook(__FILE__, 'activate_tapfiliate');
register_deactivation_hook(__FILE__, 'deactivate_tapfiliate');

if (is_admin()) {
    add_action('admin_init', 'admin_init_tapfiliate');
    add_action('admin_menu', 'admin_menu_tapfiliate');
}

if (!is_admin()) {
    add_action('wpeasycart_order_success_pre', 'tapfiliate_render_wpeasycart_conversion_code');
    add_action('wp_enqueue_scripts', 'tapfiliate');
}

// Add settings link to plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $url = admin_url('options-general.php?page=tapfiliate');
    $linkText = __('Settings');
    $links[] = "<a href=\"{$url}\">{$linkText}</a>";

    return $links;
});
