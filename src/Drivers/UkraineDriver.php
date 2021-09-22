<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class UkraineDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://bank.gov.ua/en/markets/exchangerates';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'ukraine';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_UAH;

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
        $this->date = $date;
        $respond = Http::get(static::URI, $this->queryString($date));
        if ($respond->ok()) {
            $this->html = $respond->body();
            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    /**
     *
     */
    private function parseResponse()
    {
        $this->data = [];

        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($this->html);
        $xpath = new DOMXpath($dom);

        libxml_clear_errors();

        $tableRows = $xpath->query('//table[@id="exchangeRates"]/tbody/tr');
        foreach ($tableRows as $row => $tr) {
            foreach ($tr->childNodes as $td) {
                $this->data[$row][] = $this->clearRow($td->nodeValue);
            }
            $this->data[$row] = array_values(array_filter($this->data[$row]));
        }

        $this->data = array_map(function ($item) {
            return [
                'no' => null,
                'code' => $item[1],
                'date' => $this->date->format('Y-m-d'),
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat($item[2]),
                'rate' => $this->stringToFloat($item[4]),
            ];
        }, $this->data);
    }

    /**
     * @param DateTime $date
     * @return array
     */
    private function queryString(DateTime $date): array
    {
        return [
            'date' => $date->format('Y-m-d'),
            'period' => 'daily',
        ];
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
