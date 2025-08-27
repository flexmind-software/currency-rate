<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests\Http;

use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Tests\TestCase;

class CurrencyRateControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        file_put_contents(__DIR__.'/../../database/database.sqlite', '');
        $migration = include __DIR__.'/../../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
    }

    /** @test */
    public function it_returns_rate_and_date(): void
    {
        CurrencyRate::create([
            'driver' => 'test',
            'code' => 'USD',
            'date' => '2023-10-01',
            'rate' => 4.5,
            'multiplier' => 1,
        ]);

        $this->getJson('/api/currency-rate/USD')
            ->assertStatus(200)
            ->assertExactJson([
                'value' => 4.5,
                'date' => '2023-10-01',
            ]);
    }

    /** @test */
    public function it_returns_404_for_unknown_code(): void
    {
        $this->getJson('/api/currency-rate/XYZ')->assertStatus(404);
    }
}
