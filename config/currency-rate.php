<?php

return [
    'driver' => env('FLEXMIND_CURRENCY_RATE_DRIVER', 'european-central-bank'),
    'table-name' => env('FLEXMIND_CURRENCY_RATE_TABLENAME', 'currency_rates')
];
