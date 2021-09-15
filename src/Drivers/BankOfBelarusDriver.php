<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfBelarusDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    // https://www.nbrb.by/engl/statistics/rates/ratesdaily.asp
    public const URI = 'https://api.cba.am/ExchangeRatesToCSV.ashx';
    /**
     * @var string
     */
    public const DRIVER_NAME = 'bank-of-belarus';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_BYN;

    public function downloadRates(DateTime $date)
    {
    }

    /**
     * @param DateTime $date
     *
     * @return string
     */
    private function sourceUrl(DateTime $date): string
    {
        return sprintf(
            '%s?%s',
            static::URI
        );
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function queryString(DateTime $date): array
    {
        // query send over POST method
        $queryString = [
            'Date' => $date->format('Y-m-d'),
            'Date' => $date->format('d/m/Y'),
            'Type' => 'Day',
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        return $queryString;
    }
}
