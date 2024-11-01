<?php

declare(strict_types=1);

/**
 * Check if WooCommerce is activated.
 */
function tapfiliate_is_woocommerce_activated(): bool
{
    return class_exists('WooCommerce');
}

/**
 * Check if WooCommerce v3.
 */
function tapfiliate_is_woo3(): bool
{
    if (tapfiliate_is_woocommerce_activated()) {
        global $woocommerce;

        return version_compare($woocommerce->version, '3.0', '>=');
    }

    return false;
}

/**
 * Rounding function adapted from woocommerce.
 */
function tapfiliate_woo_round(float $amount): float
{
    return (float) number_format($amount, wc_get_price_decimals(), '.', '');
}

/**
 * Check if has WooCommerce subscriptions.
 */
function tapfiliate_has_woo_subscriptions(): bool
{
    return function_exists('wcs_order_contains_subscription');
}

/**
 * Check if we have all the required webhooks.
 *
 * @throws Exception
 */
function tapfiliate_get_woocommerce_connection_status(): string
{
    $data_store = WC_Data_Store::load('webhook');
    // @phpstan-ignore-next-line
    $webhooks = $data_store->search_webhooks();

    $required_webhooks = array(
        'order.deleted',
        'order.updated',
        'order.created',
        'customer.deleted',
        'customer.updated',
        'customer.created',
    );

    if (tapfiliate_has_woo_subscriptions()) {
        $required_webhooks = array_merge($required_webhooks, array(
            'subscription.switched',
            'subscription.updated',
            'subscription.created',
            'subscription.deleted',
        ));
    }

    $current_webhooks = array_reduce(
        $webhooks,
        static function ($carry, $item) {
            $webhook = new WC_Webhook($item);
            $name = $webhook->get_name();
            if (false !== strpos($name, 'Tapfiliate')) {
                $carry[] = $webhook->get_topic();
            }

            return $carry;
        },
        array()
    );

    // If there are no webhooks we're not connected
    if (count($current_webhooks) === 0) {
        return 'none';
    }

    $missing_webhooks = array_diff($required_webhooks, array_unique($current_webhooks));

    if ($missing_webhooks !== array()) {
        if (count($required_webhooks) !== count($missing_webhooks)) {
            echo '<div class="error"><p><strong>Tapfiliate is missing the following webhooks: ' . implode(', ', $missing_webhooks) . '. You can reconnect to Tapfiliate to fix this.</strong></p></div>';
        }

        return 'partial';
    }

    return 'full';
}
