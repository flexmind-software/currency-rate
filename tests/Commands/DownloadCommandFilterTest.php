<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests\Commands;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Drivers\BaseDriver;
use FlexMindSoftware\CurrencyRate\DTO\CurrencyRateData;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use FlexMindSoftware\CurrencyRate\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class DownloadCommandFilterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../../database/database.sqlite', '');
        $migration = include __DIR__.'/../../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
        CurrencyRate::extend(FakeMultiDriver::DRIVER_NAME, fn () => new FakeMultiDriver());
    }

    /** @test */
    public function it_saves_only_specified_currencies(): void
    {
        Http::fake([
            'example.com/*' => Http::response('ok', 200),
        ]);

        $this->artisan('flexmind:currency-rate:download', [
            'date' => '2023-10-01',
            '--driver' => FakeMultiDriver::DRIVER_NAME,
            '--queue' => 'none',
            '--connection' => 'testing',
            '--currency' => 'GBP',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('currency_rates', ['code' => 'GBP']);
        $this->assertDatabaseMissing('currency_rates', ['code' => 'USD']);
    }
}

class FakeMultiDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    public const DRIVER_NAME = 'fake-multi';
    public const URI = 'https://example.com/rates';

    public CurrencyCode $currency = CurrencyCode::EUR;

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
        return 'Fake Multi Driver';
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
