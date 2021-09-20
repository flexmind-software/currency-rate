<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class TurkeyDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /** 201512/25122015.xml
     * @const string
     */
    public const URI = 'https://www.tcmb.gov.tr/kurlar/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'turkey';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_TRY;

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
        do {
            $respond = Http::get(static::URI . $this->queryString($date));
            if ($respond->ok()) {
                $this->xml = $respond->body();
            }
            $this->date = $date->sub(\DateInterval::createFromDateString('1 day'));
        } while (! $respond->ok());

        $this->parseResponse();
        $this->saveInDatabase();
    }

    /**
     * @param DateTime $date
     * @return string
     */
    private function queryString(DateTime $date): string
    {
        return $date->format('Ym/dmY') . '.xml';
    }

    private function parseResponse()
    {
        $simpleXMLElement = simplexml_load_string($this->xml);
        $no = $simpleXMLElement->attributes()->Bulten_No;
        $date = DateTime::createFromFormat('m/d/Y', (string)$simpleXMLElement->attributes()->Date)->format('Y-m-d');

        $this->data = [];
        foreach ($simpleXMLElement->Currency as $element) {
            $rate = (float)$element->CrossRateUSD ?: (float)$element->CrossRateOther;

            $this->data[] = [
                'no' => (string)$no,
                'code' => (string)$element->attributes()->CurrencyCode,
                'date' => $date,
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat((float)$element->Unit),
                'rate' => $rate,
            ];
        }
    }

    protected function saveInDatabase()
    {
        CurrencyRate::upsert($this->data, ['no', 'driver', 'code', 'date'], ['rate', 'multiplier']);
    }
}
