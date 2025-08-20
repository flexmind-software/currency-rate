<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class ChinaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'http://www.chinamoney.com.cn/r/cms/www/chinamoney/data/fx/ccpr.json';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'china';
    /**
     * @var string
     */
    public CurrencyCode $currency = CurrencyCode::CNY;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $respond = $this->fetch(static::URI, $this->queryString());
        if ($respond) {
            $this->json = json_decode($respond, true);
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
            "t" => $this->date->getTimestamp(),
            '_' => now()->getTimestamp(),
        ];
    }

    private function parseResponse()
    {
        $date = date('Y-m-d', strtotime($this->json['data']['lastDate']));

        $this->data = [];
        foreach ($this->json['records'] as $record) {
            $units = (int)$record['vrtEName'] == 0 ? 1 : (int)$record['vrtEName'];

            $this->data[] = [
                'no' => null,
                'code' => $record['foreignCName'],
                'date' => $date,
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat($units),
                'rate' => $this->stringToFloat(trim($record['price'])),
            ];
        }
    }

    public function fullName(): string
    {
        return 'CFETS - China Foreign Exchange Trade System';
    }

    public function homeUrl(): string
    {
        return 'http://www.chinamoney.com.cn/english/bmkcpr/';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.china.frequency');
    }
}
