<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
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
     * @var string
     */
    public string $currency = Currency::CUR_CZK;
    /**
     * @var array
     */
    private array $headers;

    /**
     * @param DateTime $date
     *
     * @return mixed|void
     */
    public function downloadRates(DateTime $date)
    {
        $this->date = $date;
        $sourceUrl = $this->sourceUrl($date);

        if ($fileContent = file_get_contents($sourceUrl)) {
            $explode = explode("\n", $fileContent);

            $rateList = array_map(function ($item) {
                return explode('|', $item);
            }, $explode);

            $headers = head($rateList);
            $this->parseHeader($headers);
            $this->parseRates($rateList);
            $this->saveInDatabase();
        }
    }

    /**
     * @param DateTime $date
     *
     * @return string
     */
    private function sourceUrl(DateTime $date): string
    {
        return sprintf(
            '%s?%s',
            static::URI,
            sprintf(static::QUERY_STRING, $date->format('Y'))
        );
    }

    /**
     * @param array $headers
     */
    private function parseHeader(array $headers)
    {
        $this->headers = [];
        foreach ($headers as $i => $value) {
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

    /**
     * @param array $rateList
     */
    private function parseRates(array $rateList)
    {
        $this->data = [];
        foreach ($rateList as $row => $rates) {
            if ($row === 0) {
                continue;
            }
            $date = DateTime::createFromFormat('d.m.Y', $rates[0]);
            if (! $date) {
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

    /**
     *
     */
    protected function saveInDatabase()
    {
        $toSave = [];

        $date = $this->date->format('Y-m-d');
        if (! isset($this->data[$date])) {
            $dateList = array_keys($this->data);
            $date = last($dateList);
        }

        foreach ($this->data[$date] ?? [] as $currencyCode => $rateInfo) {
            if (! count($this->config['supported-currency']) ||
                in_array(strtoupper($currencyCode), $this->config['supported-currency'])
            ) {
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
        }

        CurrencyRate::upsert($toSave, ['driver', 'code', 'date'], ['rate', 'multiplier']);
    }
}
