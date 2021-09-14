<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use SimpleXMLElement;

class EuropeanCentralBankDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @var string
     */
    public string $currency = Currency::CUR_EUR;

    /**
     * @var string
     */
    private string $driverAlias = 'european-central-bank';
    /**
     * @var array
     */
    private array $data = [];

    public function downloadRates(DateTime $date)
    {
        $this->date = $date;

        $url = $this->sourceUrl($date);

        $string = file_get_contents($url);
        $xml = new SimpleXMLElement($string);

        $this->parseDate($xml->Cube->Cube);
        $this->findByDate($date);
        $this->saveInDatabase();
    }

    private function sourceUrl(DateTime $date)
    {
        return $this->config['drivers'][$this->driverAlias]['url'];
    }

    private function parseDate($jsonData)
    {
        $jsonData = json_decode(json_encode($jsonData), true);
        foreach ($jsonData['Cube'] ?? [] as $k => $children) {
            foreach ($children as $node) {
                $this->data[$node['currency']]['date'] = $jsonData['@attributes']['time'];
                $this->data[$node['currency']]['rate'] = floatval($node['rate']);
                $this->data[$node['currency']]['multiplier'] = 1;
                $this->data[$node['currency']]['no'] = null;
                $this->data[$node['currency']]['driver'] = $this->driverAlias;
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
        if (!$date) {
            !$this->data ?: $this->data = reset($this->data);
        }

        $date = $date->format('Y-m-d');

        foreach ($this->data ?? [] as $data) {
            if (empty($data['date']) || $data['date'] !== $date) {
                continue;
            }
            $this->data[] = $data;
        }
    }

    private function saveInDatabase()
    {
        CurrencyRate::upsert($this->data, ['driver', 'code', 'date'], ['rate', 'multiplier']);
    }
}
