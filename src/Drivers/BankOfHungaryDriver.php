<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfHungaryDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    // https://www.mnb.hu/en/arfolyam-tablazat?deviza=rbCurrencyAll&devizaSelected=ZAR&datefrom=01%2F01%2F2021&datetill=15%2F09%2F2021&order=1
    /**
     * @const string
     */
    public const URI = 'https://www.mnb.hu/en/arfolyam-tablazat';
    /**
     * @var string
     */
    public const DRIVER_NAME = 'bank-of-hungary';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_HUF;

    public function downloadRates(DateTime $date)
    {
    }

    /**
     * @param DateTime $date
     *
     * @return string
     */
    private function sourceUrl(DateTime $date): string
    {
        $queryString = [
            'deviza' => 'rbCurrencyAll',
            'devizaSelected' => 'ZAR',
            'datetill' => $date->format('d/m/Y'),
            'datefrom' => $date->format('01/01/Y'),
            'order' => 1,
        ];

        return sprintf(
            '%s?%s',
            static::URI,
            http_build_query($queryString)
        );
    }
}
