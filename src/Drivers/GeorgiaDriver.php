<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
    public CurrencyCode $currency = CurrencyCode::GEL;

    /**
     * @var array
     */
    protected array $json;
    /**
     * @var array
     */
    private array $currencyList;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $respond = $this->fetch(static::URI . 'en/json');
        if ($respond) {
            $this->json = json_decode($respond, true);

            $this->getCurrencyList();

            $jsonDate = (new DateTimeImmutable())->setTimestamp(strtotime(head($this->json)['date']));

            $this->data = [];
            if ($jsonDate->diff($this->date)->days != 0) {
                $this->parseHistoricalDataResponse();
            } else {
                $this->parseCurrentDataResponse();
            }
        }

        return $this;
    }

    private function getCurrencyList()
    {
        $this->currencyList = array_column($this->json[0]['currencies'], 'code');
    }

    private function parseHistoricalDataResponse()
    {
        foreach ($this->currencyList as $currency) {
            $respond = $this->fetch(static::URI, $this->queryString($this->date, $currency));
            if ($respond) {
                $this->json = json_decode($respond, true);
                $this->parseCurrentDataResponse();
            }
        }
    }

    /**
     *
     * @param string $currency
     *
     * @return array
     */
    private function queryString(DateTimeImmutable $date, string $currency): array
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
        return __('currency-rate::description.georgia.frequency');
    }
}
