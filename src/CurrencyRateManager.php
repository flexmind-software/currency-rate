<?php

namespace FlexMindSoftware\CurrencyRate;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Drivers\NbpDriver;
use Illuminate\Support\Manager;

class CurrencyRateManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return config('currency-rate.driver') ?? 'nbp';
    }

    /**
     * Get an instance of the log driver.
     *
     * @return LogFirewallDriver
     */
    public function createNbpDriver(): CurrencyInterface
    {
        return new NbpDriver();
    }
}
