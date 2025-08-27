<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use FlexMindSoftware\CurrencyRate\Support\Logger;
use SimpleXMLElement;

/**
 *
 */
class RomaniaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     *
     * eg. https://www.bnro.ro/files/xml/years/nbrfxrates2021.xml
     */
    public const URI = 'https://www.bnro.ro/files/xml/years/nbrfxrates%s.xml';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'romania';
    /**
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::RON;
    /**
     * @var array
     */
    protected array $json;
    /**
     * @var SimpleXMLElement
     */
    private SimpleXMLElement $xml;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $respond = $this->fetch($this->sourceUrl());
        if ($respond) {
            $this->xml = $this->parseXml($respond);
            $this->parseDate();
            $this->findByDate("");
        }

        return $this;
    }

    /**
     * @return string
     */
    private function sourceUrl(): string
    {
        return sprintf(
            '%s',
            sprintf(static::URI, $this->date->format('Y')),
        );
    }

    /**
     *
     */
    private function parseDate()
    {
        $this->data = [];
        foreach ($this->xml->Body->Cube as $line) {
            $date = (string)$line->attributes()->date;
            foreach ($line->Rate as $rate) {
                $params = [
                    'date' => $date,
                    'driver' => static::DRIVER_NAME,
                    'code' => (string)$rate->attributes()->currency,
                    'rate' => (float)$rate[0],
                    'multiplier' => (int)$rate->attributes()->multiplier,
                ];

                $params['multiplier'] = ! $params['multiplier'] ? 1 : $params['multiplier'];

                $this->data[$date][] = $params;
            }
        }
    }

    /**
     * Extract rate data by date
     * If the date does not exist we force set latest data
     */
    protected function findByDate(string $label, string $dateFormat = 'Y-m-d')
    {
        if (! $this->date) {
            ! $this->data ?: $this->data = reset($this->data);
        }

        $date = $this->date->format($dateFormat);
        if (isset($this->data[$date])) {
            $this->data = $this->data[$date];
        } else {
            $this->data = last($this->data);
        }
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Banca Nationala a Romaniei';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.bnro.ro/Home.aspx';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.romania.frequency');
    }
}
