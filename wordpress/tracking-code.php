<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// External id, amount, customer id, currency
$providerQueryParameterMap = array(
    'paypal'    => array('tx', 'amt', null, 'cc'),
    'moonclerk' => array('payment_id', 'customer_id', 'amount', null),
    'chargebee' => array('invoice_id', null, 'sub_id', null),
);

/**
 * Tapfiliate shortcode function.
 */
function tapfiliate_shortcode(array $atts): string
{
    // Amount and currency query params take precedence over explicitly passed values,
    // so that those can act as defaults. Manually set query params take precedence over
    // provider query params (i.e. they override them.)
    $attributes = shortcode_atts(
        array(
            'amount'                  => null,
            'amount_query_param'      => null,
            'commission_type'         => 'default',
            'coupons'                 => null,
            'currency'                => null,
            'currency_query_param'    => null,
            'customer_id'             => null,
            'customer_id_query_param' => null,
            'customer_type'           => null,
            'external_id_query_param' => null,
            'meta_data'               => null,
            'provider'                => null,
        ),
        $atts
    );

    $options = array();

    // Get manually defined query params
    $query_parameter_external_id = $attributes['external_id_query_param'] ?? null;
    $query_parameter_conversion_amount = $attributes['amount_query_param'] ?? null;
    $query_parameter_customer_id = $attributes['customer_id_query_param'] ?? null;
    $query_parameter_currency = $attributes['currency_query_param'] ?? null;

    $external_id = $_GET[$query_parameter_external_id] ?? null;
    $customer_id = $_GET[$query_parameter_customer_id] ?? null;
    $amount = $_GET[$query_parameter_conversion_amount] ?? $attributes['amount'];
    $currency = $_GET[$query_parameter_currency] ?? $attributes['currency'];

    $external_id_arg = null !== $external_id ? "'{$external_id}'" : null;
    $amount_arg = null !== $amount ? (float) $amount : null;

    if ($customer_id) {
        $options['customer_id'] = $customer_id;
    }

    if ($coupons = $attributes['coupons']) {
        $options['coupons'] = $coupons;
    }

    if ($currency) {
        $options['currency'] = $currency;
    }

    if ($meta_data = $attributes['meta_data']) {
        // We allow multiple fields with meta_data="key1=value1&key2=value2"
        parse_str(str_replace('&amp;', '&', $attributes['meta_data']), $meta_data);
        $options['meta_data'] = $meta_data;
    }

    $customer_type = in_array($attributes['customer_type'], array('trial', 'lead', 'customer'), true) ? $attributes['customer_type'] : null;
    $is_customer_only = null !== $customer_type && null !== $customer_id;

    return tapfiliate_generate_inline_code(
        true,
        $is_customer_only,
        $customer_type,
        $external_id_arg,
        $amount_arg,
        $options,
        $attributes['commission_type'],
        array(),
        'wordpress',
        true
    );
}

add_shortcode('tapfiliate', 'tapfiliate_shortcode');

function tapfiliate_render_wordpress_code(): void
{
    $script = tapfiliate_generate_inline_code(
        false,
        false
    );

    wp_add_inline_script('tapfiliate-js', $script);
}
