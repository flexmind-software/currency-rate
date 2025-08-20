<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

/**
 *
 */
class BelarusDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     *
     * https://www.nbrb.by/Services/XmlExRatesDyn.aspx?curId=440&fromDate=9/01/2021&toDate=9/21/2021 - history
     */
    public const URI = 'https://www.nbrb.by/Services/XmlExRates.aspx';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'belarus';
    /**
     * @var string
     */
    public CurrencyCode $currency = CurrencyCode::BYN;
    /**
     * @var string
     */
    private string $xml;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $response = $this->fetch(static::URI, $this->queryString());

        if ($response) {
            $this->xml = $response;
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
            'ondate' => $this->date->format('m/d/Y'),
        ];
    }

    /**
     *
     */
    private function parseResponse()
    {
        $xml = $this->parseXml($this->xml);

        if (count($xml->Currency)) {
            $date = DateTime::createFromFormat('m/d/Y', $xml->attributes()[0])->format('Y-m-d');

            foreach ($xml->Currency as $xmlElement) {
                $this->data[] = [
                    'no' => (int)$xmlElement->attributes()[0],
                    'code' => (string)$xmlElement->CharCode,
                    'date' => $date,
                    'driver' => static::DRIVER_NAME,
                    'multiplier' => $this->stringToFloat((int)$xmlElement->Scale),
                    'rate' => $this->stringToFloat($xmlElement->Rate),
                ];
            }
        }
    }

    public function fullName(): string
    {
        return 'Natsional\'nyy bank Respubliki Belarus\'';
    }

    public function homeUrl(): string
    {
        return 'https://www.nbrb.by/';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.belarus.frequency');
    }
}
