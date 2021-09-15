<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfCanadaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    public const URI = 'https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json';

    public string $currency = Currency::CUR_CAD;

    public const DRIVER_NAME = 'bank-of-canada';
    /**
     * @var array
     */
    private array $data;

    public function downloadRates(\DateTime $date)
    {
        $this->date = $date;

        $url = $this->sourceUrl($date);

        if ($jsonFile = file_get_contents($url)) {
            $jsonFile = json_decode($jsonFile, true);
            if (blank($jsonFile['observations'])) {
                $date->sub(\DateInterval::createFromDateString('1 day'));
                $url = $this->sourceUrl($date);
                if ($jsonFile = file_get_contents($url)) {
                    $jsonFile = json_decode($jsonFile, true);
                }
            }

            $this->parseRates($jsonFile['observations'] ?? []);
            $this->saveInDatabase();
        }
    }

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    private function sourceUrl(\DateTime $date): string
    {
        return sprintf(
            '%s?start_date=%s',
            static::URI,
            $date->format('Y-m-d')
        );
    }

    /**
     * @param array $rateList
     */
    private function parseRates(array $rateList)
    {
        $this->data = [];
        $date = $this->date->format('Y-m-d');
        foreach ($rateList as $rates) {
            foreach ($rates as $key => $rate) {
                if (strpos($key, 'FX') !== false) {
                    $currency = str_replace('FX', '', $key);
                    $currency = str_replace(Currency::CUR_CAD, '', $currency);

                    $param = [];
                    $param['no'] = null;
                    $param['code'] = $currency;
                    $param['driver'] = static::DRIVER_NAME;
                    $param['date'] = $date;
                    $param['multiplier'] = 1;
                    $param['rate'] = $rate['v'];

                    $this->data[] = $param;
                }
            }
        }
    }

    private function saveInDatabase()
    {
        CurrencyRate::upsert($this->data, ['driver', 'code', 'date'], ['rate', 'multiplier']);
    }
}
