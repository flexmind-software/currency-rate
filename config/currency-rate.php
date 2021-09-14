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
        ],
        'bank-of-denmark' => [
            'url' => 'https://www.nationalbanken.dk/_vti_bin/DN/DataService.svc/CurrencyRatesHistoryXML?lang=en',
        ],
        'bank-of-estonia' => [
            'url' => 'https://www.eestipank.ee/en/exchange-rates/export/xml/latest',
        ],
        'bank-of-norway' => [
            'url' => 'https://data.norges-bank.no/api/data/EXR/B..NOK.SP?startPeriod=${todayDate}&endPeriod=${todayDate}&format=sdmx-json&locale=en',
        ],
        'bank-of-sweden' => [
            'url' => 'https://www.riksbank.se/en-gb/statistics/search-interest--exchange-rates/?c=cAverage&f=Day&from=03%2f06%2f2020&g130-SEKATSPMI=on&g130-SEKAUDPMI=on&g130-SEKBEFPMI=on&g130-SEKBRLPMI=on&g130-SEKCADPMI=on&g130-SEKCHFPMI=on&g130-SEKCNYPMI=on&g130-SEKCYPPMI=on&g130-SEKCZKPMI=on&g130-SEKDEMPMI=on&g130-SEKDKKPMI=on&g130-SEKEEKPMI=on&g130-SEKESPPMI=on&g130-SEKEURPMI=on&g130-SEKFIMPMI=on&g130-SEKFRFPMI=on&g130-SEKGBPPMI=on&g130-SEKGRDPMI=on&g130-SEKHKDPMI=on&g130-SEKHUFPMI=on&g130-SEKIDRPMI=on&g130-SEKIEPPMI=on&g130-SEKINRPMI=on&g130-SEKISKPMI=on&g130-SEKITLPMI=on&g130-SEKJPYPMI=on&g130-SEKKRWPMI=on&g130-SEKKWDPMI=on&g130-SEKLTLPMI=on&g130-SEKLVLPMI=on&g130-SEKMADPMI=on&g130-SEKMXNPMI=on&g130-SEKMYRPMI=on&g130-SEKNLGPMI=on&g130-SEKNOKPMI=on&g130-SEKNZDPMI=on&g130-SEKPLNPMI=on&g130-SEKPTEPMI=on&g130-SEKRUBPMI=on&g130-SEKSARPMI=on&g130-SEKSGDPMI=on&g130-SEKSITPMI=on&g130-SEKSKKPMI=on&g130-SEKTHBPMI=on&g130-SEKTRLPMI=on&g130-SEKTRYPMI=on&g130-SEKUSDPMI=on&g130-SEKZARPMI=on&s=Dot&to=${day}%2f${month}%2f${fullYear}&export=csv',
        ],
        'european-central-bank' => [
            'url' => 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml',
        ]
    ]
];

if (!is_array($params['supported-currency'])) {
    $params['supported-currency'] = explode(',', $params['supported-currency']);
}

return $params;
