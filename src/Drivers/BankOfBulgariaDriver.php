<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

/**
 *
 */
class BankOfBulgariaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.bnb.bg/Statistics/StExternalSector/StExchangeRates/StERForeignCurrencies/index.htm';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'bank-of-bulgaria';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_BGN;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $this->date = $date;

        $url = $this->sourceUrl($date);

        if ($fileContent = file_get_contents($url)) {
            $explode = explode("\n", $fileContent);

            $rateList = array_map(function ($item) {
                return explode(',', $item);
            }, $explode);

            $rateList = array_filter($rateList, function ($item) {
                return count($item) === 6;
            });

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
        $queryString = http_build_query([
            'download' => 'csv',
            'search' => '',
            'lang' => 'EN',
        ]);

        return sprintf('%s?%s', static::URI, $queryString);
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

            $param = [];
            $param['no'] = null;
            $param['code'] = $rates[2];
            $param['driver'] = static::DRIVER_NAME;
            $param['date'] = DateTime::createFromFormat('d.m.Y', $rates[0])->format('Y-m-d');
            $param['multiplier'] = $rates[3];
            $param['rate'] = $rates[4];

            $this->data[] = $param;
        }
    }
}
