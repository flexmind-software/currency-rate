<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
            'date' => $this->date->format('Y-m-d'),
            'period' => 'daily',
        ];
    }

    /**
     *
     */
    private function parseResponse()
    {
        $this->data = [];

        $xpath = $this->htmlParse();

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

    public function fullName(): string
    {
        return 'Natsionalʹnyy bank Ukrayiny';
    }

    public function homeUrl(): string
    {
        return 'https://www.bank.gov.ua/';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.ukraine.frequency');
    }
}
