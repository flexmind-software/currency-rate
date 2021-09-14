<?php

namespace FlexMindSoftware\CurrencyRate;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfBulgariaDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfCanadaDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfCzechRepublicDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfDenmarkDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfEstoniaDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfNorwayDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfPolandDriver;
use FlexMindSoftware\CurrencyRate\Drivers\BankOfSwedenDriver;
use FlexMindSoftware\CurrencyRate\Drivers\EuropeanCentralBankDriver;
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

    /**
     * @return BankOfDenmarkDriver
     */
    public function createBankOfDenmarkDriver(): CurrencyInterface
    {
        return new BankOfDenmarkDriver();
    }

    /**
     * @return BankOfEstoniaDriver
     */
    public function createBankOfEstoniaDriver(): CurrencyInterface
    {
        return new BankOfEstoniaDriver();
    }

    /**
     * @return BankOfNorwayDriver
     */
    public function createBankOfNorwayDriver(): CurrencyInterface
    {
        return new BankOfNorwayDriver();
    }

    /**
     * @return BankOfSwedenDriver
     */
    public function createBankOfSwedenDriver(): CurrencyInterface
    {
        return new BankOfSwedenDriver();
    }

    /**
     * @return EuropeanCentralBankDriver
     */
    public function createEuropeanCentralBankDriver(): CurrencyInterface
    {
        return new EuropeanCentralBankDriver();
    }
}
