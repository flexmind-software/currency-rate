<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

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

    public function __construct()
    {
        $this->config = config('currency-rate');
    }
}
