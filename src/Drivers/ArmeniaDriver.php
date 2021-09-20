<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
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
    public const URI = 'https://www.cba.am/en/sitepages/ExchangeArchive.aspx';
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
    private string $html;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
//        $response = Http::get(static::URI, $this->queryString($date));
//        if ($response->ok()) {
//            $this->html = $response->body();
//            $this->parseResponse();
//        }
    }

    /**
     *
     */
    private function parseResponse()
    {
        $this->data = [];

        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($this->html, LIBXML_NOERROR & LIBXML_NOWARNING);
        $xpath = new DOMXpath($dom);

        libxml_clear_errors();

        $tableRows = $xpath->query('//*[@id="WebPartWPQ6"]');
        $div = $tableRows->item(0);
//        foreach ($tableRows as $row => $tr) {
//            foreach ($tr->childNodes as $td) {
//                $this->data[$row][] = $this->clearRow($td->nodeValue);
//            }
//            $this->data[$row] = array_values(array_filter($this->data[$row]));
//        }
//
//        $this->data = array_map(function ($item) {
//            [$multiplier, $code] = explode(' ', $item[1]);
//
//            return [
//                'no' => null,
//                'code' => $code,
//                'date' => $this->date->format('Y-m-d'),
//                'driver' => static::DRIVER_NAME,
//                'multiplier' => floatval($multiplier),
//                'rate' => floatval($item[2]),
//            ];
//        }, $this->data);
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function queryString(DateTime $date): array
    {
        return [
            'ISOCodes' => 'AED,ARS,AUD,BGN,BRL,BYN,CAD,CHF,CNY,CZK,DKK,EGP,EUR,GBP,GEL,HKD,HUF,ILS,INR,IRR,ISK,' .
                'JPY,KGS,KRW,KWD,KZT,LBP,LTL,LVL,MDL,MXN,NOK,PLN,RON,RUB,SAR,SEK,SGD,SKK,SYP,TJS,TMT,TRY,UAH,' .
                'USD,UZS,XDR',
            'DateTo' => $date->format('d/m/Y'),
            'DateFrom' => $date->format('01/01/Y'),
//            'order' => 1,
        ];
    }
}
