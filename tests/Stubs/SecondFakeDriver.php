<?php

namespace FlexMindSoftware\CurrencyRate\Tests\Stubs;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Drivers\BaseDriver;
use FlexMindSoftware\CurrencyRate\DTO\CurrencyRateData;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class SecondFakeDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    public const DRIVER_NAME = 'fake2';
    public const URI = 'https://example.com/other';
    public CurrencyCode $currency = CurrencyCode::EUR;

    public function grabExchangeRates(): self
    {
        $this->fetch(static::URI);

        $this->data[] = new CurrencyRateData(
            driver: self::DRIVER_NAME,
            code: 'GBP',
            date: '2023-10-01',
            rate: 1.2,
            multiplier: 1,
            no: null,
        );

        return $this;
    }

    public function fullName(): string
    {
        return 'Second Fake Driver';
    }

    public function homeUrl(): string
    {
        return 'https://example.com';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
