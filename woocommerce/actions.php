<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (get_option('tap_wc_enabled')) {
    function tapfiliate_woocommerce_add_webhook_additional_data($payload, $resource, $resource_id, $id)
    {
        if ('order' !== $resource) {
            return $payload;
        }

        $order = new WC_Order($resource_id);
        $discount = $order->get_total_discount();
        $commissions = tapfiliate_woocommerce_get_commissions_for_order($order, $discount);

        $payload['tap_commissions'] = $commissions;

        if ($coupons = $order->get_coupon_codes()) {
            $payload['tap_coupons'] = array_values($coupons);
        }

        return $payload;
    }

    add_action('woocommerce_webhook_payload', 'tapfiliate_woocommerce_add_webhook_additional_data', 10, 4);
}
