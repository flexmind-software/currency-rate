<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

/**
 *
 */
class ArmeniaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    // https://api.cba.am/ExchangeRatesToCSV.ashx?DateFrom=2021-01-01&DateTo=2021-09-15&ISOCodes=AED,ARS,AUD,BGN,BRL,BYN,CAD,CHF,CNY,CZK,DKK,EGP,EUR,GBP,GEL,HKD,HUF,ILS,INR,IRR,ISK,JPY,KGS,KRW,KWD,KZT,LBP,LTL,LVL,MDL,MXN,NOK,PLN,RON,RUB,SAR,SEK,SGD,SKK,SYP,TJS,TMT,TRY,UAH,USD,UZS,XDR
    /**
     * @const string
     */
    public const URI = 'https://api.cba.am/ExchangeRatesToCSV.ashx';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'armenia';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_AMD;

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
        $queryString = [
            'ISOCodes' => 'AED,ARS,AUD,BGN,BRL,BYN,CAD,CHF,CNY,CZK,DKK,EGP,EUR,GBP,GEL,HKD,HUF,ILS,INR,IRR,ISK,' .
                'JPY,KGS,KRW,KWD,KZT,LBP,LTL,LVL,MDL,MXN,NOK,PLN,RON,RUB,SAR,SEK,SGD,SKK,SYP,TJS,TMT,TRY,UAH,' .
                'USD,UZS,XDR',
            'DateTo' => $date->format('d/m/Y'),
            'DateFrom' => $date->format('01/01/Y'),
            'order' => 1,
        ];

        return sprintf(
            '%s?%s',
            static::URI,
            http_build_query($queryString)
        );
    }

    public function fullName(): string
    {
        return '';
    }

    public function homeUrl(): string
    {
        return '';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
