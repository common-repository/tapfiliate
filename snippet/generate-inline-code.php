<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function tapfiliate_generate_inline_code(bool $is_converting, bool $customer_only, ?string $customer_type = null, ?string $external_id_arg = null, ?float $amount_arg = null, array $options = array(), ?string $commission_type = null, array $commissions = array(), string $integration = 'wordpress', bool $shortcode = false)
{
    if ($customer_only) {
        $customer_type = $customer_type ?: 'customer';
    }

    $tap_account_id = get_option('tap_account_id');
    $external_id_arg = apply_filters('tapfiliate_snippet_external_id', $external_id_arg);
    if (null === $external_id_arg) {
        $external_id_arg = 'null';
    }
    $amount_arg = apply_filters('tapfiliate_snippet_amount', $amount_arg);
    if (null === $amount_arg) {
        $amount_arg = 'null';
    }
    $is_converting = apply_filters('tapfiliate_snippet_is_converting', $is_converting);
    $customer_only = apply_filters('tapfiliate_snippet_customer_only', $customer_only);
    $customer_type = apply_filters('tapfiliate_snippet_customer_type', $customer_type);
    $customer_id_arg = apply_filters('tapfiliate_snippet_customer_id', $options['customer_id'] ?? null);
    if (null === $customer_id_arg) {
        $customer_id_arg = 'null';
    }
    $commission_type = apply_filters('tapfiliate_snippet_commission_type', $commission_type);
    $commissions = apply_filters('tapfiliate_snippet_commissions', $commissions);
    $shortcode = apply_filters('tapfiliate_snippet_shortcode', $shortcode);
    $options = apply_filters('tapfiliate_snippet_options', $options);

    if ($customer_only) {
        unset($options['customer_id'], $options['currency']);
    }

    $options_arg = count($options) > 0 ? json_encode($options) : json_encode($options, JSON_FORCE_OBJECT);
    $commissions_arg = count($commissions) > 0 ? json_encode($commissions) : json_encode($commissions, JSON_FORCE_OBJECT);

    ob_start();

    require_once __DIR__ . '/tracking-snippet.php';
    $script = ob_get_contents();
    ob_end_clean();

    return apply_filters('tapfiliate_snippet', $script);
}
