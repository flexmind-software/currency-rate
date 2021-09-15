<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfDenmarkDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    public const URI = 'https://www.nationalbanken.dk/_vti_bin/DN/DataService.svc/CurrencyRatesHistoryXML';
    public const QUERY_STRING = 'lang=en';

    /**
     * @var string
     */
    public string $currency = Currency::CUR_DKK;

    /**
     * @var string
     */
    private string $driverAlias = 'bank-of-denmark';

    public function downloadRates(DateTime $date)
    {
        $this->date = $date;

        $url = $this->sourceUrl($date);
        $xml = simplexml_load_file($url, "SimpleXMLElement", LIBXML_NOCDATA);
        $xml = json_encode($xml);
        $json = json_decode($xml, true);

        $this->parseDate($json);
        $this->findByDate($date);
        $this->saveInDatabase();
    }

    private function sourceUrl(DateTime $date)
    {
        return sprintf(
            '%s?%s',
            static::URI,
            static::QUERY_STRING
        );
    }

    private function parseDate(array $jsonData)
    {
        foreach ($jsonData['Cube'] ?? [] as $children) {
            foreach ($children as $k => $child) {
                if (! empty($child['@data']['time'])) {
                    $this->data[$k]['time'] = $child['@data']['time'];

                    foreach ($child['Cube'] ?? [] as $node) {
                        if (! empty($node['@data'])) {
                            $this->data[$k]['rates'][$node['@data']['currency']] = $node['@data']['rate'];
                        }
                    }
                }
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
            if (empty($data['time']) || $data['time'] !== $date) {
                continue;
            }

            $this->data = $data;
        }
    }

    private function saveInDatabase()
    {
//        CurrencyRate::upsert($this->data, ['driver', 'code', 'date'], ['rate', 'multiplier']);
    }
}
