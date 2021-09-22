<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class AzerbaijanDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'http://www.cbr.ru/scripts/XML_daily.asp';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'azerbaijan';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_AZN;

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
        $respond = Http::get(static::URI, ['date_req' => $date->format('d.m.Y')]);
        if ($respond->ok()) {
            $this->xml = $respond->body();

            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    private function parseResponse()
    {
        $xmlElement = simplexml_load_string($this->xml, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_decode(json_encode($xmlElement), true);
        $date = DateTime::createFromFormat('d.m.Y', $json['@attributes']['Date'])->format('Y-m-d');

        foreach ($json['Valute'] ?? [] as $item) {
            $line = [
                'no' => null,
                'code' => $item['CharCode'],
                'date' => $date,
                'driver' => static::DRIVER_NAME,
                'multiplier' => floatval($item['Nominal']),
                'rate' => $this->stringToFloat($item['Value']),
            ];

            $this->data[] = $line;
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
