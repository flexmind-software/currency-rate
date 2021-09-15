<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfNorwayDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    public const URI = 'https://data.norges-bank.no/api/data/EXR/B..NOK.SP';
    public const QUERY_STRING = 'startPeriod=%s&endPeriod=%s&format=sdmx-json&locale=en';

    /**
     * @var string
     */
    public string $currency = Currency::CUR_DKK;

    /**
     * @var string
     */
    private string $driverAlias = 'bank-of-norway';

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
            static::URI,
            static::QUERY_STRING
        );
    }
}
