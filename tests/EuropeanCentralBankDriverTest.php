<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Drivers\EuropeanCentralBankDriver;

class EuropeanCentralBankDriverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
    }

    /** @test */
    public function it_parses_ecb_response()
    {
        $driver = (new class extends EuropeanCentralBankDriver {
            protected function fetch(string $url, array $query = []): ?string
            {
                return file_get_contents(__DIR__.'/Fixtures/ecb.xml');
            }
        })->setDataTime(new DateTimeImmutable('2021-09-24'));

        $driver->grabExchangeRates();
        $data = $driver->retrieveData();

        $record = $data[0];

        $this->assertSame('JPY', $record->code);
        $this->assertEquals(129.66, $record->rate);
        $this->assertSame(1.0, $record->multiplier);
    }
}

