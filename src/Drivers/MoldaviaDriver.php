<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use Exception;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class MoldaviaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.bnm.md/en/official_exchange_rates';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'moldavia';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_MDL;

    /**
     * @var string
     */
    private string $xml;

    /**
     * @param DateTime $date
     *
     * @return void
     * @throws Exception
     */
    public function downloadRates(DateTime $date)
    {
        $respond = Http::get(static::URI, $this->queryString($date));
        if ($respond->ok()) {
            $this->xml = $respond->body();

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
            'date' => $date->format('d.m.Y'),
        ];
    }

    /**
     * @throws Exception
     */
    private function parseResponse()
    {
        $xml = new SimpleXMLElement($this->xml);
        $date = Datetime::createFromFormat('d.m.Y', $xml->attributes()->Date)->format('d.m.Y');

        $this->data = [];
        foreach ($xml->Valute as $xmlElement) {
            $this->data[] = [
                'no' => (int)$xmlElement->NumCode,
                'code' => (string)$xmlElement->CharCode,
                'date' => $date,
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat((int)$xmlElement->Nominal),
                'rate' => $this->stringToFloat($xmlElement->Value),
            ];
        }
    }

    public function fullName(): string
    {
        return 'Banca Naţională a Moldovei';
    }

    public function homeUrl(): string
    {
        return 'http://www.bnm.md/';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
