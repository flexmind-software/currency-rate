<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Drivers\BulgariaDriver;

class BulgariaDriverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
    }

    /** @test */
    public function it_parses_bulgaria_response()
    {
        $driver = (new class () extends BulgariaDriver {
            protected function fetch(string $url, array $query = []): ?string
            {
                return file_get_contents(__DIR__.'/Fixtures/bulgaria.csv');
            }
        })->setDataTime(new DateTimeImmutable('2021-09-24'));

        $driver->grabExchangeRates();
        $data = $driver->retrieveData();

        $usd = collect($data)->firstWhere('code', 'USD');
        $jpy = collect($data)->firstWhere('code', 'JPY');

        $this->assertNotNull($usd);
        $this->assertSame('USD', $usd->code);
        $this->assertEquals(1, $usd->multiplier);
        $this->assertEquals(1.6500, $usd->rate);

        $this->assertNotNull($jpy);
        $this->assertSame('JPY', $jpy->code);
        $this->assertEquals(100, $jpy->multiplier);
        $this->assertEquals(150.0000, $jpy->rate);
    }
}
