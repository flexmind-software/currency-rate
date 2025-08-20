<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $respond = $this->fetch(static::URI, $this->queryString());
        if ($respond) {
            $this->html = $respond;
            $this->parseResponse();
        }

        return $this;
    }

    /**
     * @return array
     */
    private function queryString(): array
    {
        return [
            'UniDbQuery.Posted' => 'True',
            'UniDbQuery.To' => $this->date->format('d/m/Y'),
        ];
    }

    private function parseResponse()
    {
        $this->data = [];

        $xpath = $this->htmlParse();

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
        $exchangeDate = DateTime::createFromFormat('d/m/Y', $match[2])->format('Y-m-d');

        $this->data = array_map(function ($item) use ($exchangeDate) {
            return [
                'no' => null,
                'code' => $item[1],
                'date' => $exchangeDate,
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
        return __('currency-rate::description.russia.frequency');
    }
}
