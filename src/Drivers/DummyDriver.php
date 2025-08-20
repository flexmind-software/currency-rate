<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class DummyDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = '';
    /**
     * @const string
     */
    public const DRIVER_NAME = '';
    /**
     * @var string
     */
    public CurrencyCode $currency = CurrencyCode::EUR;

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
     *
     *
     * @return array
     */
    private function queryString(): array
    {
        return [];
    }

    private function parseResponse()
    {
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
        return __('currency-rate::description.dummy.frequency');
    }
}
