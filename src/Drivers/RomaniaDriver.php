<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;
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
     * @var string
     */
    public string $currency = Currency::CUR_RON;
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
        $respond = Http::get($this->sourceUrl());
        if ($respond->ok()) {
            $xml = $respond->body();
            $this->xml = simplexml_load_string($xml);
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

    public function fullName(): string
    {
        return 'Banca Nationala a Romaniei';
    }

    public function homeUrl(): string
    {
        return 'https://www.bnro.ro/Home.aspx';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.romania.frequency');
    }
}
