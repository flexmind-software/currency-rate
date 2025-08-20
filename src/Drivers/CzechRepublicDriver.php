<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

/**
 *
 */
class CzechRepublicDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/rok.txt';
    /**
     * @const string
     */
    public const QUERY_STRING = 'rok=%s';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'czech-republic';
    /**
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::CZK;
    /**
     * @var array
     */
    private array $headers;
    /**
     * @var array|false[]|string[][]
     */
    private array $rateList;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $sourceUrl = $this->sourceUrl();

        $fileContent = $this->fetch($sourceUrl);
        if ($fileContent) {

            $explode = explode("\n", $fileContent);

            $this->rateList = array_map(function ($item) {
                return explode('|', $item);
            }, $explode);

            $this->parseHeader();
            $this->parseRates();
            $this->prepareData();
        }

        return $this;
    }

    /**
     * @return string
     */
    private function sourceUrl(): string
    {
        return sprintf(
            '%s?%s',
            static::URI,
            sprintf(static::QUERY_STRING, $this->date->format('Y'))
        );
    }

    /**
     * @param
     */
    private function parseHeader()
    {
        $headerList = head($this->rateList);

        $this->headers = [];
        foreach ($headerList as $i => $value) {
            if ($i === 0) {
                continue;
            }
            [$multiplier, $code] = explode(' ', $value);
            $this->headers[$i] = [
                'multiplier' => $multiplier,
                'code' => $code,
            ];
        }
    }

    private function parseRates()
    {
        $this->data = [];
        foreach ($this->rateList as $row => $rates) {
            if ($row === 0) {
                continue;
            }
            $date = DateTimeImmutable::createFromFormat('d.m.Y', $rates[0]);
            if (! $date) {
                break;
            }

            $item = [];
            foreach ($rates as $key => $value) {
                if ($key === 0) {
                    continue;
                }

                $item[$this->headers[$key]['code']]['multiplier'] = $this->headers[$key]['multiplier'];
                $item[$this->headers[$key]['code']]['rate'] = $this->stringToFloat($value);
            }

            $this->data[$date->format('Y-m-d')] = $item;
        }
    }

    private function prepareData()
    {
        $toSave = [];

        $date = $this->date->format('Y-m-d');
        if (! isset($this->data[$date])) {
            $dateList = array_keys($this->data);
            $date = last($dateList);
        }

        foreach ($this->data[$date] ?? [] as $currencyCode => $rateInfo) {
            $item = [
                'no' => null,
                'driver' => static::DRIVER_NAME,
                'code' => strtoupper($currencyCode),
                'date' => $date,
                'multiplier' => $rateInfo['multiplier'],
                'rate' => $rateInfo['rate'],
            ];

            $toSave[] = $item;
        }

        $this->data = $toSave;
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Ceska Narodni Banka';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.cnb.cz';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.czech-republic.frequency');
    }
}
