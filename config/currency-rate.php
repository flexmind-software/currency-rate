<?php

$params = [
    'driver' => env('FLEXMIND_CURRENCY_RATE_DRIVER', 'bank-of-poland'),
    'table-name' => env('FLEXMIND_CURRENCY_RATE_TABLENAME', 'currency_rates'),
    'supported-currency' => env('FLEXMIND_CURRENCY_RATE_SUPPORTED_CURRENCY', []),
    'drivers' => [
        'bank-of-poland' => [
            'url' => 'http://www.nbp.pl/kursy/xml/'
        ],
        'bank-of-czech-republic' => [
            'url' => 'http://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/rok.txt'
        ],
        'bank-of-canada' => [
            'url' => 'https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'
        ],
        'bank-of-bulgaria' => [
            'url' => 'http://www.bnb.bg/Statistics/StExternalSector/StExchangeRates/StERForeignCurrencies/index.htm?download=csv&search=&lang=EN'
        ]
    ]
];

if (!is_array($params['supported-currency'])) {
    $params['supported-currency'] = explode(',', $params['supported-currency']);
}

return $params;
