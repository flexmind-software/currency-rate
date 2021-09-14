<?php

namespace FlexMindSoftware\CurrencyRate;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FlexMindSoftware\CurrencyRate\CurrencyRate
 */
class CurrencyRateFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'currency-rate';
    }
}
