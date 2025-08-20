<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Drivers\PolandDriver;

class PolandDriverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
    }

    /** @test */
    public function it_parses_poland_response()
    {
        $driver = (new class extends PolandDriver {
            protected function fetch(string $url, array $query = []): ?string
            {
                if (str_ends_with($url, 'dir.txt')) {
                    return 'a001z210924';
                }
                return file_get_contents(__DIR__.'/Fixtures/poland.xml');
            }
        })->setDataTime(new DateTimeImmutable('2021-09-24 13:00:00'));

        $driver->grabExchangeRates();
        $data = $driver->retrieveData();

        $usd = collect($data)->firstWhere('code', 'USD');
        $jpy = collect($data)->firstWhere('code', 'JPY');

        $this->assertNotNull($usd);
        $this->assertSame('USD', $usd->code);
        $this->assertEquals(1, $usd->multiplier);
        $this->assertEquals(3.9500, $usd->rate);

        $this->assertNotNull($jpy);
        $this->assertSame('JPY', $jpy->code);
        $this->assertEquals(100, $jpy->multiplier);
        $this->assertEquals(3.5000, $jpy->rate);
    }
}
