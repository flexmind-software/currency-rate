<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use Carbon\Carbon;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class CnbDriver implements CurrencyInterface
{
    use RateTrait;

    public string $currency = Currency::CUR_CZK;

    private string $driverAlias = 'cnb';
    /**
     * @var array
     */
    private array $config;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @var array
     */
    private array $data;

    public function __construct()
    {
        $this->config = config('currency-rate.drivers.cnb');
    }

    public function downloadRates(\DateTime $date)
    {
        $sourceUrl = $this->sourceUrl($date);

        if ($fileContent = file_get_contents($sourceUrl)) {
            $rateList = str_getcsv($fileContent, '|');

            $headers = head($rateList);
            $this->parseHeader($headers);
            $this->parseRates($rateList);
            $this->saveInDatabase();
        }
    }

    /**
     * @param Carbon $date
     *
     * @return string
     */
    private function sourceUrl(Carbon $date)
    {
        return sprintf($this->config['url'] . '?rok=%d', $date->format('Y'));
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function parseHeader(array $headers): array
    {
        $this->header = [];
        foreach ($headers as $i => $value) {
            if ($i === 0) {
                continue;
            }
            [$multiplier, $code] = explode(' ', $value);
            $header[$i] = [
                'multiplier' => $multiplier,
                'code' => $code,
            ];
        }

        return $this->header;
    }

    private function parseRates(array $rateList)
    {
        $this->data = [];
        foreach ($rateList as $row => $rates) {
            if ($row === 0) {
                continue;
            }
            $date = \DateTime::createFromFormat('d.m.Y', $rates[0]);
            if (!$date) {
                break;
            }

            $item = [];
            foreach ($rates as $key => $value) {
                if ($key === 0) {
                    continue;
                }

                $item[$this->headers[$key]['code']]['multiplier'] = $this->headers[$key]['multiplier'];
                $item[$this->headers[$key]['code']]['rate'] = floatval(str_replace(',', '.', $value));
            }

            $this->data[$date->format('Y-m-d')] = $item;
        }
    }

    private function saveInDatabase()
    {
        $toSave = [];
        foreach ($this->data as $date => $params) {
            foreach ($params as $currencyCode => $rateInfo) {
                $item = [
                    'no' => null,
                    'driver' => $this->driverAlias,
                    'code' => $currencyCode,
                    'date' => $date,
                    'multiplier' => $rateInfo['multiplier'],
                    'rate' => $rateInfo['rate']
                ];

                $toSave[] = $item;
            }
        }

        CurrencyRate::upsert($toSave, ['driver', 'code', 'date'], ['rate', 'multiplier']);
    }
}
