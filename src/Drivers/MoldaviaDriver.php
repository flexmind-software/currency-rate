<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
use Exception;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::MDL;

    /**
     * @var string
     */
    private string $xml;

    /**
     * @return self
     * @throws Exception
     */
    public function grabExchangeRates(): self
    {
        $respond = $this->fetch(static::URI, $this->queryString());
        if ($respond) {
            $this->xml = $respond;
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
            'date' => $this->date->format('d.m.Y'),
        ];
    }

    /**
     * @throws Exception
     */
    private function parseResponse()
    {
        $xml = $this->parseXml($this->xml);
        $date = DateTimeImmutable::createFromFormat('d.m.Y', $xml->attributes()->Date)->format('d.m.Y');

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

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Banca Naţională a Moldovei';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.bnm.md/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.moldavia.frequency');
    }
}
