<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class IsraelDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.boi.org.il/en/_layouts/boi/handlers/WebPartHandler.aspx';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'israel';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_ILS;

    /**
     * @var string
     */
    protected string $html;

    /**
     * @var string
     */
    private string $xml;

    private array $countryList = [];

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $this->date = $date;

        $respond = Http::get('https://www.boi.org.il/currency.xml');
        if ($respond->ok()) {
            $this->xml = $respond->body();
            $this->makeCountryMap();
        }

        $respond = Http::get(static::URI, $this->queryString($date));
        if ($respond->ok()) {
            $this->html = $respond->body();
            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    private function makeCountryMap()
    {
        $xmlElement = simplexml_load_string($this->xml, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_decode(json_encode($xmlElement), true);

        $this->countryList = [];
        foreach ($json['CURRENCY'] ?? [] as $item) {
            $this->countryList[$item['COUNTRY']] = $item['CURRENCYCODE'];
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
            'wp' => 'ExchangeRates',
            'lang' => 'en-US',
            'date' => $this->date->format('d/m/Y'),
            'graphUrl' => '/en/Markets/ExchangeRates/Pages/Chart.aspx',
            'webUrl' => '/en/Markets/ExchangeRates',
            '_' => time(),
        ];
    }

    private function parseResponse()
    {
        $this->data = [];

        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($this->html);
        $xpath = new DOMXpath($dom);

        libxml_clear_errors();

        $tableRows = $xpath->query('//*[@id="BoiCurrencyExchangeRatesTab"]/table/tr');
        foreach ($tableRows as $row => $tr) {
            foreach ($tr->childNodes as $td) {
                $this->data[$row][] = $this->clearRow($td->nodeValue);
            }
            $this->data[$row] = array_values(array_filter($this->data[$row]));
        }

        $this->data = array_filter($this->data, function ($item) {
            return count($item) === 4;
        });

        $this->data = array_map(function ($item) {
            return  [
                'no' => null,
                'code' => $this->mapCountryToIso($item[2]),
                'date' => $this->date->format('Y-m-d'),
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat($item[1]),
                'rate' => $this->stringToFloat($item[3]),
            ];
        }, $this->data);
    }

    private function mapCountryToIso(string $country)
    {
        return $this->countryList[$country] ?? null;
    }

    public function fullName(): string
    {
        return 'Bank of Israel';
    }

    public function homeUrl(): string
    {
        return 'https://www.boi.org.il/';
    }

    public function infoAboutFrequency(): string
    {
        return 'Daily';
    }
}
