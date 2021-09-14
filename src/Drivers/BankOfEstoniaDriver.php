<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfEstoniaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @var string
     */
    public string $currency = Currency::CUR_EUR;

    /**
     * @var string
     */
    private string $driverAlias = 'bank-of-estonia';

    public function downloadRates(DateTime $date)
    {
        $this->date = $date;

        $url = $this->sourceUrl($date);
    }

    private function sourceUrl(DateTime $date)
    {
        return $this->config['drivers'][$this->driverAlias]['url'];
    }
}
