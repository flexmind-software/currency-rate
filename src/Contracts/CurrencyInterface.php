<?php

namespace FlexMindSoftware\CurrencyRate\Contracts;

use DateTime;

interface CurrencyInterface
{
    /**
     * @return self
     */
    public function grabExchangeRates(): self;

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param DateTime $date
     *
     * @return float|int
     */
    public function rate(string $currencyFrom, string $currencyTo, DateTime $date);

    /**
     * @return string
     */
    public function fullName(): string;

    /**
     * @return string
     */
    public function homeUrl(): string;

    /**
     * @return string
     */
    public function infoAboutFrequency(): string;
}
