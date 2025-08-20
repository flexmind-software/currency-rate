<?php

namespace FlexMindSoftware\CurrencyRate\Tests\Stubs;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Drivers\BaseDriver;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class FakeDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    public const DRIVER_NAME = 'fake';
    public const URI = 'https://example.com/rates';
    public CurrencyCode $currency = CurrencyCode::EUR;

    public function grabExchangeRates(): self
    {
        $this->fetch(static::URI);

        $this->data[] = [
            'driver' => self::DRIVER_NAME,
            'code' => 'USD',
            'date' => '2023-10-01',
            'rate' => 1.1,
            'multiplier' => 1,
            'no' => null,
        ];

        return $this;
    }

    public function fullName(): string
    {
        return 'Fake Driver';
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
