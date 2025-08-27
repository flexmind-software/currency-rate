<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use FlexMindSoftware\CurrencyRate\Support\Logger;

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
     * @var CurrencyCode
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

    /**
     * @return string
     */
    public function fullName(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.dummy.frequency');
    }
}
