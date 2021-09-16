<?php

$params = [
    'driver' => env('FLEXMIND_CURRENCY_RATE_DRIVER', 'european-central-bank'),
    'table-name' => env('FLEXMIND_CURRENCY_RATE_TABLENAME', 'currency_rates'),
    'supported-currency' => env('FLEXMIND_CURRENCY_RATE_SUPPORTED_CURRENCY', [])
];

if (!is_array($params['supported-currency'])) {
    $params['supported-currency'] = explode(',', $params['supported-currency']);
}

return $params;
