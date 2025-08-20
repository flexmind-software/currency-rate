<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed driver(?string $driver = null)
 *
 * @see \FlexMindSoftware\CurrencyRate\CurrencyRateManager
 */
class CurrencyRateFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'currency-rate';
    }
}
