<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class RomaniaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     *
     * eg. https://www.bnro.ro/files/xml/years/nbrfxrates2021.xml
     */
    public const URI = 'https://www.bnro.ro/files/xml/years/nbrfxrates%s.xml';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'romania';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_RSD;

    /**
     * @param DateTime $date
     *
     * @return void
     */
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
