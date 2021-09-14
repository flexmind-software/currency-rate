<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfSwedenDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @var string
     */
    public string $currency = Currency::CUR_DKK;

    /**
     * @var string
     */
    private string $driverAlias = 'bank-of-sweden';

    public function downloadRates(DateTime $date)
    {

    }
}
