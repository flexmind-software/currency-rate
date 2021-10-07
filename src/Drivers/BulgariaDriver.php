<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

/**
 *
 */
class BulgariaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.bnb.bg/Statistics/StExternalSector/StExchangeRates/StERForeignCurrencies/index.htm';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'bulgaria';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_BGN;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $response = Http::get(static::URI, $this->queryString());

        if ($response->ok()) {
            $fileContent = $response->body();

            $explode = explode("\n", $fileContent);

            $rateList = array_map(function ($item) {
                return explode(',', $item);
            }, $explode);

            $rateList = array_filter($rateList, function ($item) {
                return count($item) === 6;
            });

            $this->parseRates($rateList);
        }

        return $this;
    }

    /**
     * @return array
     */
    private function queryString(): array
    {
        return [
            'download' => 'csv',
            'search' => '',
            'lang' => 'EN',
        ];
    }

    /**
     * @param array $rateList
     */
    private function parseRates(array $rateList)
    {
        $this->data = [];
        foreach ($rateList as $i => $rates) {
            if ($i <= 1) {
                continue;
            }

            $date = DateTime::createFromFormat('d.m.Y', $rates[0])->format('Y-m-d');

            $param = [];
            $param['no'] = null;
            $param['code'] = $rates[2];
            $param['driver'] = static::DRIVER_NAME;
            $param['date'] = $date;
            $param['multiplier'] = $rates[3];
            $param['rate'] = $rates[4];

            $this->data[] = $param;
        }
    }

    public function fullName(): string
    {
        return 'Bŭlgarska narodna banka';
    }

    public function homeUrl(): string
    {
        return 'https://www.bnb.bg';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
