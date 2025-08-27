<?php

return [
    'driver' => env('FLEXMIND_CURRENCY_RATE_DRIVER', 'european-central-bank'),

    'drivers' => [
        'albania',
        'armenia',
        'australia',
        'azerbaijan',
        'bceao',
        'belarus',
        'bosnia-and-herzegovina',
        'botswana',
        'bulgaria',
        'canada',
        'china',
        'croatia',
        'czech-republic',
        'denmark',
        'england',
        'european-central-bank',
        'fiji',
        'georgia',
        'hungary',
        'iceland',
        'israel',
        'macedonia',
        'moldavia',
        'norway',
        'poland',
        'romania',
        'russia',
        'serbia',
        'sweden',
        'switzerland',
        'turkey',
        'ukraine',
        'united-states',
    ],

    'table-name' => env('FLEXMIND_CURRENCY_RATE_TABLENAME', 'currency_rates'),

    'cache-ttl' => env('FLEXMIND_CURRENCY_RATE_CACHE_TTL', 3600),

    'cache_store' => env('FLEXMIND_CURRENCY_RATE_CACHE_STORE', 'array'),

    'log_channel' => env('FLEXMIND_CURRENCY_RATE_LOG_CHANNEL', null),

    'fed' => [
        'api_key' => env('FRED_API_KEY'),
    ],
    'queue_concurrency' => env('FLEXMIND_CURRENCY_RATE_QUEUE_CONCURRENCY', 10),

    'retry' => [
        'count'  => (int) env('FLEXMIND_CURRENCY_RATE_RETRY_COUNT', 3),
        'sleep'  => (int) env('FLEXMIND_CURRENCY_RATE_RETRY_SLEEP', 1000),
        'factor' => (int) env('FLEXMIND_CURRENCY_RATE_RETRY_FACTOR', 2),
    ],
];
