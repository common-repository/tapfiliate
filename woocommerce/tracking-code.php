<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function tapfiliate_render_woocommerce_code(): void
{
    $is_converting = false;
    $external_id_arg = null;
    $amount_arg = null;
    $options = array();
    $is_customer_only = false;
    $commissions = array();
    $commission_type = null;
    $use_woo_customer_id_for_lifetime = 'on' === get_option('tap_wc_use_woo_customer_id_for_lifetime');
    $customer_type = null;

    if (function_exists('is_order_received_page') && is_order_received_page() && isset($GLOBALS['order-received'])) {
        $is_converting = true;

        $isWoo3 = tapfiliate_is_woo3();

        $order_id = apply_filters('woocommerce_thankyou_order_id', absint($GLOBALS['order-received']));
        $order_key = apply_filters('woocommerce_thankyou_order_key', empty($_GET['key']) ? '' : wc_clean($_GET['key']));

        if ($order_id <= 0) {
            return;
        }

        $order = new WC_Order($order_id);
        // @phpstan-ignore-next-line
        $order_key_check = $isWoo3 ? $order->get_order_key() : $order->order_key;

        if ($order_key_check !== $order_key) {
            return;
        }
        // @phpstan-ignore-next-line
        $containsSubscription = tapfiliate_has_woo_subscriptions() && wcs_order_contains_subscription($order_id);

        $options['meta_data'] = tapfiliate_woocommerce_get_metadata_for_order($order);

        $discount = $order->get_total_discount();
        $commissions = tapfiliate_woocommerce_get_commissions_for_order($order, $discount);

        // Check if we have multiple commission types
        $unique_commission_types = array_unique(array_column($commissions, 'commission_type'));
        $is_conversion_multi = count($unique_commission_types) > 1;

        // Get commission type if single commission type
        $commission_type = 1 === count($unique_commission_types) ? $unique_commission_types[0] : 'default';

        // Get Customer Id
        $customerId = $use_woo_customer_id_for_lifetime ? resolve_customer_id($order) : $order->get_billing_email();

        // Set options
        if ($coupons = $order->get_coupon_codes()) {
            $options['coupons'] = array_values($coupons);
        }

        if ($customerId) {
            $options['customer_id'] = $customerId;
        }

        if ($currency = $order->get_currency()) {
            $options['currency'] = $currency;
        }
        // @phpstan-ignore-next-line
        $external_id_arg = $isWoo3 ? $order->get_id() : $order->id;
        $amount_arg = $order->get_subtotal() - $discount;

        $is_customer_only = $containsSubscription && 0.00 === $amount_arg;
        $customer_type = $is_customer_only ? 'trial' : 'customer';
    }

    $script = tapfiliate_generate_inline_code(
        $is_converting,
        $is_customer_only,
        $customer_type,
        (string) $external_id_arg,
        $amount_arg,
        $options,
        $commission_type,
        $commissions,
        'woocommerce'
    );

    wp_add_inline_script('tapfiliate-js', $script);
}
