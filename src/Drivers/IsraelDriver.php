<?php
declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::ILS;

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
        $respond = $this->fetch('https://www.boi.org.il/currency.xml');
        if ($respond) {
            $this->xml = $respond;
            $this->makeCountryMap();
        }

        $respond = $this->fetch(static::URI, $this->queryString());
        if ($respond) {
            $this->html = $respond;
            $this->parseResponse();
        }

        return $this;
    }

    private function makeCountryMap(): void
    {
        $xmlElement = $this->parseXml($this->xml);

        $this->countryList = [];
        foreach ($xmlElement->CURRENCY as $item) {
            $country = (string) $item->COUNTRY;
            $code = (string) $item->CURRENCYCODE;
            $this->countryList[$country] = $code;
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

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Bank of Israel';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.boi.org.il/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.israel.frequency');
    }
}
