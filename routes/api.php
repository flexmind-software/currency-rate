<?php

use FlexMindSoftware\CurrencyRate\Http\Controllers\CurrencyRateController;
use Illuminate\Support\Facades\Route;

Route::get('/api/currency-rate/{code}', [CurrencyRateController::class, 'show']);
