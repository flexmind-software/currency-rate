<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use FlexMindSoftware\CurrencyRate\Support\Logger;

class TurkeyDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /** 201512/25122015.xml
     *
     * @const string
     */
    public const URI = 'https://www.tcmb.gov.tr/kurlar/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'turkey';
    /**
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::TRY;

    /**
     * @var string
     */
    private string $xml;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        do {
            $respond = $this->fetch(static::URI . $this->queryString());
            if ($respond) {
                $this->xml = $respond;
            }
            $this->date = $this->date->sub(DateInterval::createFromDateString('1 day'));
        } while (! $respond);

        $this->parseResponse();

        return $this;
    }

    /**
     * @return string
     */
    private function queryString(): string
    {
        return $this->date->format('Ym/dmY') . '.xml';
    }

    private function parseResponse()
    {
        $simpleXMLElement = $this->parseXml($this->xml);
        $no = $simpleXMLElement->attributes()->Bulten_No;
        $date = DateTimeImmutable::createFromFormat('m/d/Y', (string)$simpleXMLElement->attributes()->Date)->format('Y-m-d');

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

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Türkiye Cumhuriyet Merkez Bankası';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.tcmb.gov.tr/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.turkey.frequency');
    }
}
