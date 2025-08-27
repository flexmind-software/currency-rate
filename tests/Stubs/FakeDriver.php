<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests\Stubs;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Drivers\BaseDriver;
use FlexMindSoftware\CurrencyRate\DTO\CurrencyRateData;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class FakeDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    public const DRIVER_NAME = 'fake';
    public const URI = 'https://example.com/rates';
    /**
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::EUR;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $this->fetch(static::URI);

        $this->data[] = new CurrencyRateData(
            driver: self::DRIVER_NAME,
            code: 'USD',
            date: '2023-10-01',
            rate: 1.1,
            multiplier: 1,
            no: null,
        );

        return $this;
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Fake Driver';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://example.com';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return '';
    }
}
