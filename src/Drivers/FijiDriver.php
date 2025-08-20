<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use SimpleXLSX;

class FijiDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.rbf.gov.fj/wp-content/uploads/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'fiji';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_FJD;

    /**
     * @var array
     */
    private array $currencyMap = [
        "YEN" => Currency::CUR_CNY,
        "CHF" => Currency::CUR_CHF,
        "A$" => Currency::CUR_AUD,
        "NZ$" => Currency::CUR_NZD,
        "US$" => Currency::CUR_USD,
        "EURO" => Currency::CUR_EUR,
    ];

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $respond = $this->fetch(static::URI . $this->urlString());
        if ($respond) {
            $this->html = $respond;
            $this->parseResponse();
        }

        return $this;
    }

    /**
     *
     *
     * @return string
     */
    private function urlString(): string
    {
        return sprintf(
            '%s/%s/8.8-Exchange-Rates-Daily-5.xlsx',
            $this->date->format('Y'),
            $this->date->format('m')
        );
    }

    private function parseResponse()
    {
        $dir = sys_get_temp_dir() . '/' . self::DRIVER_NAME . '/';
        $tmp = @tempnam($dir, md5(uniqid(microtime(true)))) . '.xlsx';

        // Write the zipped content inside
        file_put_contents($tmp, $this->html);

        if ($xlsx = SimpleXLSX::parse($tmp)) {
            $items = $xlsx->rows();

            $items = array_values(
                array_filter($items, function ($item) {
                    return ! blank($item[1]);
                })
            );

            $headers = array_shift($items);
            $headers = array_map(function ($item) {
                return $this->currencyMap[trim($item)] ?? null;
            }, $headers);

            array_map(function ($items) use ($headers) {
                foreach ($items as $i => $rate) {
                    if (! blank($headers[$i])) {
                        $this->data[] = [
                            'no' => null,
                            'code' => $headers[$i],
                            'date' => date('Y-m-d', strtotime($items[0])),
                            'driver' => static::DRIVER_NAME,
                            'multiplier' => $this->stringToFloat(1),
                            'rate' => $this->stringToFloat(trim($rate)),
                        ];
                    }
                }
            }, $items);
        }
    }

    public function fullName(): string
    {
        return 'Reserve Bank of Fiji';
    }

    public function homeUrl(): string
    {
        return 'https://www.rbf.gov.fj/';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.fiji.frequency');
    }
}
