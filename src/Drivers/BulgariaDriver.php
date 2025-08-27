<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::BGN;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $fileContent = $this->fetch(static::URI, $this->queryString());

        if ($fileContent) {
            $rateList = $this->parseCsv($fileContent, ',');

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

            $date = DateTimeImmutable::createFromFormat('d.m.Y', $rates[0])->format('Y-m-d');

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

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Bŭlgarska narodna banka';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.bnb.bg';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.bulgaria.frequency');
    }
}
