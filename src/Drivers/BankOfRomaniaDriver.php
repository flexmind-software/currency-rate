<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfRomaniaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    // https://www.bnro.ro/files/xml/years/nbrfxrates2021.xml
    public const URI = 'https://www.bnro.ro/files/xml/years/nbrfxrates%s.xml';
    /**
     * @var string
     */
    public const DRIVER_NAME = 'bank-of-romania';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_RSD;

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
            '%s',
            sprintf(static::URI, $date->format('Y')),
        );
    }
}
