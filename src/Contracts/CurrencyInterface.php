<?php

namespace FlexMindSoftware\CurrencyRate\Contracts;

use DateTimeImmutable;

interface CurrencyInterface
{
    /**
     * @return self
     */
    public function grabExchangeRates(): self;

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param DateTimeImmutable $date
     *
     * @return float|int
     */
    public function rate(string $currencyFrom, string $currencyTo, DateTimeImmutable $date);

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
