<?php

declare(strict_types=1);

function tapfiliate_render_wpeasycart_conversion_code(int $order_id): void
{
    global $wpdb;
    $commissions = array();
    $options = array();
    // @phpstan-ignore-next-line
    $db = new ec_db_admin();
    // @phpstan-ignore-next-line
    $order = $db->get_order_row_admin($order_id);
    $external_id_arg = $order->order_id ?? null;
    $amount_arg = $order->grand_total ?? null;

    $commission_type = 'default';

    $customer_id = $order->user_email ?? null;

    if ($customer_id) {
        $options['customer_id'] = $customer_id;
    }
    $currency = $GLOBALS['currency']->get_currency_code() ?? 'USD';

    if ($currency = esc_attr($currency)) {
        $options['currency'] = $currency;
    }

    $customer_type = 'customer';

    $script = tapfiliate_generate_inline_code(
        true,
        false,
        $customer_type,
        (string) $external_id_arg,
        (float) $amount_arg,
        $options,
        $commission_type,
        $commissions,
        'wp-easy-cart'
    );

    echo '<script>' . $script . '</script>';
}
