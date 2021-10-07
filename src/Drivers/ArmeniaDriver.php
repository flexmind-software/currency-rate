<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

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
     * @var string
     */
    private string $csvPlain;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $response = Http::get(static::URI, $this->queryString($this->date));
        if ($response->ok()) {
            $this->csvPlain = $response->body();
            $this->parseResponse();
        }

        return $this;
    }

    /**
     *
     *
     * @return array
     */
    private function queryString(DateTime $date): array
    {
        $date = $this->lastDate ?? $date;

        $dateTo = $date->format('Y-m-d');
        $dateFrom = $date->sub(DateInterval::createFromDateString('1 month'))->format('Y-m-d');

        return [
            'ISOCodes' => 'AED,ARS,AUD,BGN,BRL,BYN,CAD,CHF,CNY,CZK,DKK,EGP,EUR,GBP,GEL,HKD,HUF,ILS,INR,IRR,ISK,' .
                'JPY,KGS,KRW,KWD,KZT,LBP,LTL,LVL,MDL,MXN,NOK,PLN,RON,RUB,SAR,SEK,SGD,SKK,SYP,TJS,TMT,TRY,UAH,' .
                'USD,UZS,XDR',
            'DateTo' => $dateTo,
            'DateFrom' => $dateFrom,
        ];
    }

    /**
     *
     */
    private function parseResponse()
    {
        $this->data = [];

        $lines = explode("\n", $this->csvPlain);
        $lines = array_map(function ($item) {
            return explode(',', $item);
        }, $lines);
        $head = head($lines);
        $head[0] = null;

        foreach ($lines as $l => $line) {
            if ($l === 0) {
                continue;
            }

            foreach ($line as $i => $value) {
                if (isset($head[$i]) && ($code = $head[$i])) {
                    $this->data[] = [
                        'no' => null,
                        'code' => $code,
                        'date' => DateTime::createFromFormat('d/m/Y', $line[0])->format('Y-m-d'),
                        'driver' => static::DRIVER_NAME,
                        'multiplier' => $this->stringToFloat(1),
                        'rate' => $this->stringToFloat($value),
                    ];
                }
            }
        }
    }

    public function fullName(): string
    {
        return 'Hayastani Hanrapetut’yan Kentronakan Bank';
    }

    public function homeUrl(): string
    {
        return 'https://www.cba.am/';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
