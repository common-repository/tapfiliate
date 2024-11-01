<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function tapfiliate_woocommerce_get_commissions_for_order(WC_Order $order, float $discount): array
{
    $commissions = array();
    foreach ($order->get_items() as $item) {
        // If total is zero the item has a full discount applied
        // @phpstan-ignore-next-line
        $item_subtotal = tapfiliate_woo_round((float) $item->get_subtotal());
        if (0.00 === $item_subtotal) {
            $sub_amount = 0;
        } else {
            $proportional_discount = ($item_subtotal / tapfiliate_woo_round($order->get_subtotal())) * $discount;
            $sub_amount = tapfiliate_woo_round($item_subtotal - $proportional_discount);
        }
        // @phpstan-ignore-next-line
        $product_id = $item->get_product_id();
        $product = new WC_Product($product_id);
        $tapfiliate_product_commission_type = $product->get_meta('tapfiliate_product_commission_type');

        $category_commission_type = null;
        if (!$tapfiliate_product_commission_type && $categories = wp_get_post_terms($product_id, 'product_cat')) {
            // We always use the "latest" category as the category commission type
            foreach ($categories as $category) {
                $category_commission_type = get_term_meta($category->term_id, 'tapfiliate_category_commission_type', true);
            }
        }

        $commissions[] = array(
            'commission_type' => ($tapfiliate_product_commission_type ?: $category_commission_type) ?: 'default',
            'sub_amount'      => $sub_amount,
        );
    }

    return $commissions;
}

function tapfiliate_woocommerce_get_metadata_for_order(WC_Order $order): array
{
    $i = 1;
    $meta_data = array();
    foreach ($order->get_items() as $item) {
        $key = sprintf('product%d', $i++);
        $line_item = sprintf('%s - qty: %s', $item['name'], $item['qty']);
        $meta_data[$key] = $line_item;
    }

    return $meta_data;
}

function resolve_customer_id(WC_Order $order): ?string
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'wc_customer_lookup';
    $customerId = $order->get_customer_id();

    // If user not logged use resolve customer id logic
    if (0 === $customerId) {
        // Resolve id by email
        $sql = $wpdb->prepare("SELECT customer_id FROM {$tablename} WHERE user_id IS NOT NULL AND email = %s LIMIT 1", $order->get_billing_email());
    } else {
        $sql = $wpdb->prepare("SELECT customer_id FROM {$tablename} WHERE user_id = %d LIMIT 1", $customerId);
    }

    return $wpdb->get_var($sql);
}
