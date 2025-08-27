<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::AMD;

    /**
     * @var string
     */
    private string $csvPlain;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $response = $this->fetch(static::URI, $this->queryString($this->date));
        if ($response) {
            $this->csvPlain = $response;
            $this->parseResponse();
        }

        return $this;
    }

    /**
     *
     *
     * @return array
     */
    private function queryString(DateTimeImmutable $date): array
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

        $lines = $this->parseCsv($this->csvPlain, ',');
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
                        'date' => DateTimeImmutable::createFromFormat('d/m/Y', $line[0])->format('Y-m-d'),
                        'driver' => static::DRIVER_NAME,
                        'multiplier' => $this->stringToFloat(1),
                        'rate' => $this->stringToFloat($value),
                    ];
                }
            }
        }
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Hayastani Hanrapetut’yan Kentronakan Bank';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.cba.am/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.armenia.frequency');
    }
}
