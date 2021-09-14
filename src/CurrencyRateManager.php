<?php

namespace FlexMindSoftware\CurrencyRate;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfBulgariaDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfCanadaDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfCzechRepublicDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfPolandDriver;
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
        return config('currency-rate.driver') ?? 'bank-of-poland';
    }

    /**
     * @return BankOfPolandDriver
     */
    public function createBankOfPolandDriver(): CurrencyInterface
    {
        return new BankOfPolandDriver();
    }

    /**
     * @return BankOfCzechRepublicDriver
     */
    public function createBankOfCzechRepublicDriver(): CurrencyInterface
    {
        return new BankOfCzechRepublicDriver();
    }

    /**
     * @return BankOfCzechRepublicDriver
     */
    public function createBankOfCanadaDriver(): CurrencyInterface
    {
        return new BankOfCanadaDriver();
    }

    /**
     * @return BankOfBulgariaDriver
     */
    public function createBankOfBulgariaDriver(): CurrencyInterface
    {
        return new BankOfBulgariaDriver();
    }
}
