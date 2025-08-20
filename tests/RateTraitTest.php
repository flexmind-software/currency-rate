<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTime;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;

class RateTraitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();

        CurrencyRate::create([
            'driver' => TestDriver::DRIVER_NAME,
            'date' => '2021-01-01',
            'code' => Currency::CUR_USD,
            'rate' => 1.2,
            'multiplier' => 1,
        ]);

        CurrencyRate::create([
            'driver' => TestDriver::DRIVER_NAME,
            'date' => '2021-01-01',
            'code' => Currency::CUR_PLN,
            'rate' => 4.5,
            'multiplier' => 1,
        ]);

    }

    /** @test */
    public function it_calculates_exchange_rate()
    {
        $driver = new TestDriver();

        $rate = $driver->rate(Currency::CUR_USD, Currency::CUR_PLN, new DateTime('2021-01-01'));

        $this->assertEquals(1.2 / 4.5, $rate);
    }

    /** @test */
    public function it_uses_base_currency_when_calculating()
    {
        $driver = new TestDriver();

        $rate = $driver->rate(Currency::CUR_EUR, Currency::CUR_PLN, new DateTime('2021-01-01'));

        $this->assertEquals(1 / 4.5, $rate);
    }
}

class TestDriver
{
    use \FlexMindSoftware\CurrencyRate\Models\RateTrait;

    public const DRIVER_NAME = 'test-driver';

    public string $currency = Currency::CUR_EUR;
}
