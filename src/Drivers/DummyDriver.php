<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

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
    public string $currency = Currency::CUR_EUR;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $respond = Http::get(static::URI, $this->queryString());
        if ($respond->ok()) {
            $this->html = $respond->body();
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
