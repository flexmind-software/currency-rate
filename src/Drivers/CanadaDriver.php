<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
    public CurrencyCode $currency = CurrencyCode::CAD;
    /**
     * @var mixed
     */
    private $jsonFile;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $this->getObservation();
        $this->parseRates();

        return $this;
    }

    private function getObservation()
    {
        do {
            $url = $this->sourceUrl();
            $response = $this->fetch($url);
            if ($response) {
                $this->jsonFile = json_decode($response, true);
                if (blank($this->jsonFile['observations'])) {
                    $this->date = $this->date->sub(DateInterval::createFromDateString('1 day'));
                }
            }
        } while (count((array)$this->jsonFile['observations']) === 0);
    }

    /**
     * @return string
     */
    private function sourceUrl(): string
    {
        return sprintf(
            '%s?start_date=%s',
            static::URI,
            $this->date->format('Y-m-d')
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
                    $rowCurrency = str_replace(CurrencyCode::CAD->value, '', $rowCurrency);

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
        return 'Banqueu du Canada';
    }

    public function homeUrl(): string
    {
        return 'https://www.bankofcanada.ca/';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.canada.frequency');
    }
}
