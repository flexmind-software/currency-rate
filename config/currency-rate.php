<?php

$params = [
    'driver' => env('FLEXMIND_CURRENCY_RATE_DRIVER', 'bank-of-poland'),
    'table-name' => env('FLEXMIND_CURRENCY_RATE_TABLENAME', 'currency_rates'),
    'supported-currency' => env('FLEXMIND_CURRENCY_RATE_SUPPORTED_CURRENCY', []),
    'drivers' => [
        'bank-of-poland' => [
            'url' => 'http://www.nbp.pl/kursy/xml/'
        ],
        'bank-of-chech-republic' => [
            'url' => 'http://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/rok.txt'
        ],
        'bank-of-canada' => [
            'url' => 'https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'
        ]
    ]
];

if (!is_array($params['supported-currency'])) {
    $params['supported-currency'] = explode(',', $params['supported-currency']);
}

return $params;
