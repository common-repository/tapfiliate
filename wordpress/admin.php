<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add a link to the settings page on the plugins.php page.
 */
function tapfiliate_settings_link(array $links)
{
    return array_merge(array('<a href="' . esc_url(admin_url('/options-general.php?page=tapfiliate')) . '">' . __('Settings', 'textdomain') . '</a>'), $links);
}

add_action('plugin_action_links_' . plugin_basename(__FILE__), 'tapfiliate_settings_link');
