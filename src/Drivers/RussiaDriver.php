<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class RussiaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.cbr.ru/eng/currency_base/daily/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'russia';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_RUB;

    /**
     * @var string
     */
    protected string $html;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $respond = Http::get(static::URI, $this->queryString($date));
        if ($respond->ok()) {
            $this->html = $respond->body();
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
            'UniDbQuery.Posted' => 'True',
            'UniDbQuery.To' => $date->format('d/m/Y'),
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

        $tableRows = $xpath->query('//table[@class="data"]/tbody/tr');

        foreach ($tableRows as $row => $tr) {
            foreach ($tr->childNodes as $td) {
                $this->data[$row][] = $this->clearRow($td->nodeValue);
            }
            $this->data[$row] = array_values(array_filter($this->data[$row]));
        }

        $this->data = array_filter($this->data, function ($item) {
            return $item[0] != 'Num сode';
        });

        $h3 = $xpath->query('//h2[@class="h3"]')->item(0)->nodeValue;
        preg_match('/(.*)([0-9]{2}\/[0-9]{2}\/[0-9]{4})(.+)/im', $h3, $match);
        $date = DateTime::createFromFormat('d/m/Y', $match[2])->format('Y-m-d');

        $this->data = array_map(function ($item) use ($date) {
            return [
                'no' => null,
                'code' => $item[1],
                'date' => $date ,
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat($item[2]),
                'rate' => $this->stringToFloat($item[4]),
            ];
        }, $this->data);
    }

    public function fullName(): string
    {
        return 'Central Bank of the Russian Federation';
    }

    public function homeUrl(): string
    {
        return 'https://www.cbr.ru/';
    }

    public function infoAboutFrequency(): string
    {
        return 'Daily';
    }
}
