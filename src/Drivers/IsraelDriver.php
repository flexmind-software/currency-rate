<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

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
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $respond = Http::get('https://www.boi.org.il/currency.xml');
        if ($respond->ok()) {
            $this->xml = $respond->body();
            $this->makeCountryMap();
        }

        $respond = Http::get(static::URI, $this->queryString());
        if ($respond->ok()) {
            $this->html = $respond->body();
            $this->parseResponse();
        }

        return $this;
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
     * @return array
     */
    private function queryString(): array
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

        $xpath = $this->htmlParse();

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
            return [
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
        return __('currency-rate::description.israel.frequency');
    }
}
