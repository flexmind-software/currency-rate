<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use Exception;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class EuropeanCentralBankDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'european-central-bank';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_EUR;

    /**
     * @param DateTime $date
     *
     * @return void
     * @throws Exception
     */
    public function downloadRates(DateTime $date)
    {
        $this->date = $date;

        $respond = Http::get(static::URI);
        if ($respond->ok()) {
            $string = $respond->body();
            $xml = new SimpleXMLElement($string);

            $this->parseDate($xml->Cube->Cube);
            $this->findByDate($date);
            $this->saveInDatabase();
        }
    }

    /**
     * @param SimpleXMLElement $jsonData
     */
    private function parseDate(SimpleXMLElement $jsonData)
    {
        $jsonData = json_decode(json_encode($jsonData), true);
        foreach ($jsonData['Cube'] ?? [] as $k => $children) {
            foreach ($children as $node) {
                $this->data[$node['currency']]['date'] = $jsonData['@attributes']['time'];
                $this->data[$node['currency']]['rate'] = floatval($node['rate']);
                $this->data[$node['currency']]['multiplier'] = 1;
                $this->data[$node['currency']]['no'] = null;
                $this->data[$node['currency']]['driver'] = static::DRIVER_NAME;
                $this->data[$node['currency']]['code'] = $node['currency'];
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
        if (! $date) {
            ! $this->data ?: $this->data = reset($this->data);
        }

        $date = $date->format('Y-m-d');

        foreach ($this->data ?? [] as $data) {
            if (empty($data['date']) || $data['date'] !== $date) {
                continue;
            }
            $this->data[] = $data;
        }
    }
}
