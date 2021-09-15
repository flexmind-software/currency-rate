<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfBosniaAndHerzegovinaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    // https://www.cbbh.ba/CurrencyExchange/GetJsonForPeriod?dateFrom=Sun,%2028%20Feb%202021%2023:00:00%20GMT&dateTo=Wed,%2030%20Jun%202021%2022:00:00%20GMT
    public const URI = 'https://www.cbbh.ba/CurrencyExchange/GetJsonForPeriod';
    /**
     * @var string
     */
    public const DRIVER_NAME = 'bank-of-belarus';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_BAM;

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
        $queryString = [
            'dateFrom' => 'Sun,%2028%20Feb%202021%2023:00:00%20GMT',
            'dateTo' => 'Wed,%2030%20Jun%202021%2022:00:00%20GMT',
        ];

        return sprintf(
            '%s?%s',
            static::URI,
            http_build_query($queryString)
        );
    }
}
