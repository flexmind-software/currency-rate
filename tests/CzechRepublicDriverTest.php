<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Drivers\CzechRepublicDriver;

class CzechRepublicDriverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
    }

    /** @test */
    public function it_parses_czech_response()
    {
        $driver = (new class () extends CzechRepublicDriver {
            protected function fetch(string $url, array $query = []): ?string
            {
                return file_get_contents(__DIR__.'/Fixtures/czech.txt');
            }
        })->setDataTime(new DateTimeImmutable('2021-09-24'));

        $driver->grabExchangeRates();
        $data = $driver->retrieveData();

        $usd = collect($data)->firstWhere('code', 'USD');
        $jpy = collect($data)->firstWhere('code', 'JPY');

        $this->assertNotNull($usd);
        $this->assertSame('USD', $usd->code);
        $this->assertEquals(1, $usd->multiplier);
        $this->assertEquals(21.300, $usd->rate);

        $this->assertNotNull($jpy);
        $this->assertSame('JPY', $jpy->code);
        $this->assertEquals(100, $jpy->multiplier);
        $this->assertEquals(195.000, $jpy->rate);
    }
}
