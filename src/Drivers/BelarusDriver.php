<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;

/**
 *
 */
class BelarusDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.nbrb.by/Services/XmlExRates.aspx';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'belarus';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_BYN;
    /**
     * @var string
     */
    private string $xml;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $this->date = $date;

        $response = Http::get(static::URI, $this->queryString($date));

        if ($response->ok()) {
            $this->xml = $response->body();
            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function queryString(DateTime $date): array
    {
        return [
            'ondate' => $date->format('m/d/Y')
        ];
    }

    /**
     *
     */
    private function parseResponse()
    {
        $xml = simplexml_load_string($this->xml);

        if (count($xml->Currency)) {
            $date = DateTime::createFromFormat('m/d/Y', $xml->attributes()[0])->format('Y-m-d');

            foreach ($xml->Currency as $xmlElement) {
                $this->data[] = [
                    'no' => (int)$xmlElement->attributes()[0],
                    'code' => (string)$xmlElement->CharCode,
                    'date' => $date,
                    'driver' => static::DRIVER_NAME,
                    'multiplier' => $this->stringToFloat((int)$xmlElement->Scale),
                    'rate' => $this->stringToFloat($xmlElement->Rate),
                ];
            }
        }
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
