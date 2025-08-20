<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class AzerbaijanDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'http://www.cbr.ru/scripts/XML_daily.asp';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'azerbaijan';
    /**
     * @var string
     */
    public CurrencyCode $currency = CurrencyCode::AZN;

    /**
     * @var string
     */
    private string $xml;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $respond = $this->fetch(static::URI, ['date_req' => $this->date->format('d.m.Y')]);
        if ($respond) {
            $this->xml = $respond;
            $this->parseResponse();
        }

        return $this;
    }

    private function parseResponse(): void
    {
        $xmlElement = $this->parseXml($this->xml);
        $date = DateTimeImmutable::createFromFormat('d.m.Y', (string) $xmlElement['Date'])->format('Y-m-d');

        foreach ($xmlElement->Valute as $item) {
            $this->data[] = [
                'no' => null,
                'code' => (string) $item->CharCode,
                'date' => $date,
                'driver' => static::DRIVER_NAME,
                'multiplier' => (float) $item->Nominal,
                'rate' => $this->stringToFloat((string) $item->Value),
            ];
        }
    }

    public function fullName(): string
    {
        return 'Azerbaijan';
    }

    public function homeUrl(): string
    {
        return 'https://www.cbar.az';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.azerbaijan.frequency');
    }
}
