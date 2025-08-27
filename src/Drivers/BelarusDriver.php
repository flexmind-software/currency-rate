<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
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
     * @var CurrencyCode
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
            $date = DateTimeImmutable::createFromFormat('m/d/Y', (string) $xml->attributes()[0])->format('Y-m-d');

            foreach ($xml->Currency as $xmlElement) {
                $this->data[] = [
                    'no' => (string) $xmlElement->attributes()[0],
                    'code' => (string)$xmlElement->CharCode,
                    'date' => $date,
                    'driver' => static::DRIVER_NAME,
                    'multiplier' => $this->stringToFloat((string) $xmlElement->Scale),
                    'rate' => $this->stringToFloat((string) $xmlElement->Rate),
                ];
            }
        }
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Natsional\'nyy bank Respubliki Belarus\'';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.nbrb.by/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.belarus.frequency');
    }
}
