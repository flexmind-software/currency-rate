<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

/**
 *
 */
class CanadaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'canada';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_CAD;
    /**
     * @var mixed
     */
    private $jsonFile;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $this->date = $date;

        $this->getObservation();
        $this->parseRates();
        $this->saveInDatabase();
    }

    private function getObservation()
    {
        do {
            $url = $this->sourceUrl($this->date);
            $response = Http::get($url);
            if ($response->ok()) {
                $this->jsonFile = $response->json();
                if (blank($this->jsonFile['observations'])) {
                    $this->date->sub(DateInterval::createFromDateString('1 day'));
                }
            }
        } while (count((array)$this->jsonFile['observations']) === 0);
    }

    /**
     * @param DateTime $date
     *
     * @return string
     */
    private function sourceUrl(DateTime $date): string
    {
        return sprintf(
            '%s?start_date=%s',
            static::URI,
            $date->format('Y-m-d')
        );
    }

    private function parseRates()
    {
        $this->data = [];
        $date = $this->date->format('Y-m-d');
        foreach ($this->jsonFile['observations'] as $rates) {
            foreach ($rates as $key => $rate) {
                if (strpos($key, 'FX') !== false) {
                    $rowCurrency = str_replace('FX', '', $key);
                    $rowCurrency = str_replace(Currency::CUR_CAD, '', $rowCurrency);

                    $param = [];
                    $param['no'] = null;
                    $param['code'] = $rowCurrency;
                    $param['driver'] = static::DRIVER_NAME;
                    $param['date'] = $date;
                    $param['multiplier'] = 1;
                    $param['rate'] = $rate['v'];

                    $this->data[] = $param;
                }
            }
        }
    }

    public function fullName(): string
    {
        return '';
    }

    public function homeUrl(): string
    {
        return '';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
