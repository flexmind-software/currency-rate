<?php

$params = [
    'driver' => env('FLEXMIND_CURRENCY_RATE_DRIVER', 'nbp'),
    'table-name' => env('FLEXMIND_CURRENCY_RATE_TABLENAME', 'currency_rates'),
    'supported-currency' => env('FLEXMIND_CURRENCY_RATE_SUPPORTED_CURRENCY', []),
    'drivers' => [
        'nbp' => [
            'url' => 'http://www.nbp.pl/kursy/xml/'
        ],
        'cnb' => [
            'url' => 'http://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/rok.txt'
        ]
    ]
];

if (!is_array($params['supported-currency'])) {
    $params['supported-currency'] = explode(',', $params['supported-currency']);
}

return $params;
