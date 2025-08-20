<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use Illuminate\Support\Facades\DB;

class RateTraitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');

        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();

        CurrencyRate::create([
            'driver' => TestDriver::DRIVER_NAME,
            'date' => '2021-01-01',
            'code' => CurrencyCode::USD->value,
            'rate' => 1.2,
            'multiplier' => 1,
        ]);

        CurrencyRate::create([
            'driver' => TestDriver::DRIVER_NAME,
            'date' => '2021-01-01',
            'code' => CurrencyCode::PLN->value,
            'rate' => 4.5,
            'multiplier' => 1,
        ]);

    }

    /** @test */
    public function it_calculates_exchange_rate()
    {
        $driver = new TestDriver();

        $rate = $driver->rate(CurrencyCode::USD, CurrencyCode::PLN, new DateTimeImmutable('2021-01-01'));

        $this->assertEquals(1.2 / 4.5, $rate);
    }

    /** @test */
    public function it_uses_base_currency_when_calculating()
    {
        $driver = new TestDriver();

        $rate = $driver->rate(CurrencyCode::EUR, CurrencyCode::PLN, new DateTimeImmutable('2021-01-01'));

        $this->assertEquals(1 / 4.5, $rate);
    }

    /** @test */
    public function it_accepts_string_currency_codes()
    {
        $driver = new TestDriver();

        $rate = $driver->rate(CurrencyCode::USD->value, CurrencyCode::PLN->value, new DateTimeImmutable('2021-01-01'));

        $this->assertEquals(1.2 / 4.5, $rate);
    }

    /** @test */
    public function it_caches_results_within_request()
    {
        $reflection = new \ReflectionClass(TestDriver::class);
        $property = $reflection->getProperty('rateCache');
        $property->setAccessible(true);
        $property->setValue(null, []);

        $queries = 0;
        DB::listen(function ($query) use (&$queries) {
            if (str_contains($query->sql, 'currency_rates')) {
                $queries++;
            }
        });

        $driver = new TestDriver();

        $driver->rate(CurrencyCode::USD, CurrencyCode::PLN, new DateTimeImmutable('2021-01-01'));
        $driver->rate(CurrencyCode::USD, CurrencyCode::PLN, new DateTimeImmutable('2021-01-01'));

        $this->assertSame(1, $queries);
    }
}

class TestDriver
{
    use \FlexMindSoftware\CurrencyRate\Models\RateTrait;

    public const DRIVER_NAME = 'test-driver';

    public CurrencyCode $currency = CurrencyCode::EUR;
}
