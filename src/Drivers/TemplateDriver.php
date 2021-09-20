<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class TemplateDriver extends BaseDriver implements CurrencyInterface
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
     * @var string
     */
    private string $xml;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $respond = Http::get(static::URI, $this->queryString($date));
        if ($respond->ok()) {
            $this->xml = $respond->body();

            $this->parseResponse();
//            $this->saveInDatabase();
        }
    }

    private function parseResponse()
    {
    }

    /**
     * @param DateTime $date
     * @return array
     */
    private function queryString(DateTime $date): array
    {
        return [];
    }
}
