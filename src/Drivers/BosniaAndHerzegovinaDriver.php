<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

/**
 *
 */
class BosniaAndHerzegovinaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    // https://www.cbbh.ba/CurrencyExchange/GetJsonForPeriod?dateFrom=Sun,%2028%20Feb%202021%2023:00:00%20GMT&dateTo=Wed,%2030%20Jun%202021%2022:00:00%20GMT
    /**
     * @const string
     */
    public const URI = 'https://www.cbbh.ba/CurrencyExchange/GetJson';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'bosnia-and-herzegovina';
    /**
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::BAM;
    /**
     * @var array|mixed
     */
    protected array $jsonData;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $response = $this->fetch(static::URI, $this->getQueryString());
        if ($response) {
            $this->jsonData = json_decode($response, true);
            $this->parseResponse();
        }

        return $this;
    }

    /**
     * @return array
     */
    private function getQueryString(): array
    {
        return [
            'date' => $this->date->format("m/d/Y 00:00:00"),
        ];
    }

    private function parseResponse()
    {
        foreach ($this->jsonData['CurrencyExchangeItems'] ?? [] as $item) {
            $this->data[] = [
                'no' => $this->jsonData['Number'],
                'code' => $item['AlphaCode'],
                'date' => date('Y-m-d', strtotime($this->jsonData['Date'])),
                'driver' => static::DRIVER_NAME,
                'multiplier' => floatval($item['Units']),
                'rate' => $this->stringToFloat($item['Middle']),
            ];
        }
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Centralna Banka Bosne I Hergegovine';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.cbbh.ba/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.bosnia-and-herzegovina.frequency');
    }
}
