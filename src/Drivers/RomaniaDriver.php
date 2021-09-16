<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
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
    public string $currency = Currency::CUR_RSD;
    /**
     * @var array
     */
    private array $json;
    /**
     * @var SimpleXMLElement
     */
    private SimpleXMLElement $xml;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $response = Http::get($this->sourceUrl($date));
        if ($response->ok()) {
            $xml = $response->body();
            $this->xml = simplexml_load_string($xml);
            $this->parseDate();
            $this->findByDate($date);
            $this->saveInDatabase();
        }
    }


    /**
     * @param DateTime $date
     *
     * @return string
     */
    private function sourceUrl(DateTime $date): string
    {
        return sprintf(
            '%s',
            sprintf(static::URI, $date->format('Y')),
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
                    'multiplier' => (int)$rate->attributes()->multiplier
                ];

                $params['multiplier'] = !$params['multiplier'] ? 1 : $params['multiplier'];

                $this->data[$date][] = $params;
            }
        }
    }

    /**
     * Extract rate data by date
     * If the date does not exist we force set latest data
     *
     * @param DateTime|null $date
     */
    private function findByDate(?DateTime $date = null)
    {
        if (!$date) {
            !$this->data ?: $this->data = reset($this->data);
        }

        $date = $date->format('Y-m-d');
        if (isset($this->data[$date])) {
            $this->data = $this->data[$date];
        } else {
            $this->data = last($this->data);
        }
    }
}
