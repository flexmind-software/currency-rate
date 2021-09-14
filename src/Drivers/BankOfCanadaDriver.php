<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BankOfCanadaDriver implements CurrencyInterface
{
    use RateTrait;

    public string $currency = Currency::CUR_CAD;

    private string $driverAlias = 'bank-of-canada';
    /**
     * @var array
     */
    private array $config;
    /**
     * @var \DateTime
     */
    private \DateTime $date;

    public function __construct()
    {
        $this->config = config('currency-rate');
    }

    public function downloadRates(\DateTime $date)
    {
        $this->date = $date;

        $url = $this->sourceUrl($date);

        if ($jsonFile = file_get_contents($url)) {
            $this->parseRates($jsonFile);
            $this->saveInDatabase();
        }
    }

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    private function sourceUrl(\DateTime $date): string
    {
        return sprintf(
            '%s?start_date=%s',
            $this->config['drivers'][$this->driverAlias]['url'],
            $date->format('Y-m-d')
        );
    }

    private function parseRates(array $jsonFile)
    {
    }

    private function saveInDatabase()
    {
    }
}
