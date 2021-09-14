<?php

$params = [
    'driver' => env('FLEXMIND_CURRENCY_RATE_DRIVER', 'nbp'),
    'supported-currency' => env('FLEXMIND_CURRENCY_RATE_SUPPORTED_CURRENCY', [
        'thb',
        'usd',
        'aud',
        'hkd',
        'cad',
        'nzd',
        'sgd',
        'eur',
        'huf',
        'chf',
        'gbp',
        'uah',
        'jpy',
        'czk',
        'dkk',
        'isk',
        'nok',
        'sek',
        'hrk',
        'ron',
        'bgn',
        'try',
        'ltl',
        'ils',
        'clp',
        'php',
        'mxn',
        'zar',
        'brl',
        'myr',
        'rub',
        'idr',
        'inr',
        'krw',
        'cny',
        'xdr'
    ]),
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
