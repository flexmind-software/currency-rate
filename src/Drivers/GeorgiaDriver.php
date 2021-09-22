<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class GeorgiaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     *
     * // https://nbg.gov.ge/gw/api/ct/monetarypolicy/currencies/?currencies=EUR&date=2021-08-31
     * // https://nbg.gov.ge/gw/api/ct/monetarypolicy/currencies/en/json - current
     */
    public const URI = 'https://nbg.gov.ge/gw/api/ct/monetarypolicy/currencies/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'georgia';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_GEL;

    /**
     * @var array
     */
    private array $json;
    /**
     * @var array
     */
    private array $currencyList;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $this->date = $date;
        $respond = Http::get(static::URI . 'en/json');
        if ($respond->ok()) {
            $this->json = $respond->json();

            $this->getCurrencyList();

            $jsonDate = new DateTime();
            $jsonDate->setTimestamp(strtotime(head($this->json)['date']));

            $this->data = [];
            if ($jsonDate->diff($date)->days != 0) {
                $this->parseHistoricalDataResponse();
            } else {
                $this->parseCurrentDataResponse();
            }

            $this->saveInDatabase();
        }
    }

    private function getCurrencyList()
    {
        $this->currencyList = array_column($this->json[0]['currencies'], 'code');
    }

    private function parseHistoricalDataResponse()
    {
        foreach ($this->currencyList as $currency) {
            $respond = Http::get(static::URI, $this->queryString($this->date, $currency));
            if ($respond->ok()) {
                $this->json = $respond->json();
                $this->parseCurrentDataResponse();
            }
        }
    }

    /**
     * @param DateTime $date
     * @param string $currency
     *
     * @return array
     */
    private function queryString(DateTime $date, string $currency): array
    {
        return [
            'currencies' => $currency,
            'date' => $date->format('Y-m-d'),
        ];
    }

    private function parseCurrentDataResponse()
    {
        $date = strtotime(head($this->json)['date']);
        foreach ($this->json[0]['currencies'] as $currency) {
            $this->data[] = [
                'no' => null,
                'code' => $currency['code'],
                'date' => date('Y-m-d', $date),
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat($currency['quantity']),
                'rate' => $this->stringToFloat($currency['rate']),
            ];
        }
    }

    public function fullName(): string
    {
        return 'The National Bank of Georgia';
    }

    public function homeUrl(): string
    {
        return 'https://nbg.gov.ge/';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
