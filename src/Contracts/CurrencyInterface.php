<?php

namespace FlexMindSoftware\CurrencyRate\Contracts;

interface CurrencyInterface
{
    /**
     * @param \DateTime $date
     *
     * @return mixed
     */
    public function downloadRates(\DateTime $date);

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param \DateTime $date
     *
     * @return float|int
     */
    public function rate(string $currencyFrom, string $currencyTo, \DateTime $date);
}
