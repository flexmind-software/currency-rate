<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;

class CurrencyRateSaveInTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
    }

    /** @test */
    public function it_saves_records_to_database()
    {
        $data = [
            [
                'driver' => 'test',
                'code' => 'USD',
                'date' => '2023-10-01',
                'rate' => 1.1,
                'multiplier' => 1,
                'no' => null,
            ],
            [
                'driver' => 'test',
                'code' => 'PLN',
                'date' => '2023-10-01',
                'rate' => 4.4,
                'multiplier' => 1,
                'no' => null,
            ],
        ];

        CurrencyRate::saveIn($data, 'testing');

        $this->assertDatabaseHas('currency_rates', ['code' => 'USD']);
        $this->assertDatabaseHas('currency_rates', ['code' => 'PLN']);
    }
}
