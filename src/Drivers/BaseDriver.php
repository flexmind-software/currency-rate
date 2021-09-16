<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;

abstract class BaseDriver
{
    /**
     * @var \DateTime
     */
    protected \DateTime $date;
    /**
     * @var array
     */
    protected array $config;

    /**
     * @var array
     */
    protected array $data = [];

    public function __construct()
    {
        $this->config = config('currency-rate');
    }

    protected function saveInDatabase()
    {
        CurrencyRate::upsert($this->data, ['driver', 'code', 'date'], ['rate', 'multiplier']);
    }
}
