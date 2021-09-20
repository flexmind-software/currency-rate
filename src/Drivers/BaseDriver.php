<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;

abstract class BaseDriver
{
    /**
     * @var DateTime
     */
    protected DateTime $date;
    /**
     * @var array
     */
    protected array $config;

    /**
     * @var array
     */
    protected array $data = [];
    /**
     * @var DateTime|null
     */
    protected ?DateTime $lastDate;

    public function __construct()
    {
        $this->config = config('currency-rate');
        $this->lastDate = CurrencyRate::where('driver', static::DRIVER_NAME)->latest('date')->value('date');
    }

    protected function saveInDatabase()
    {
        CurrencyRate::upsert($this->data, ['driver', 'code', 'date'], ['rate', 'multiplier']);
    }

    /**
     * @param string|null $string
     * @return string|null
     */
    protected function clearRow(?string $string): ?string
    {
        return preg_replace('~[\r\n]+~', '', trim($string));
    }

    /**
     * @param string $string
     * @return float
     */
    protected function stringToFloat(string $string): float
    {
        return (float)str_replace(',', '.', $string);
    }
}
