<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests\Drivers;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Drivers\UnitedStatesDriver;
use FlexMindSoftware\CurrencyRate\Tests\TestCase;

class UnitedStatesDriverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../../database/database.sqlite', '');
        $migration = include __DIR__.'/../../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
    }

    /** @test */
    public function it_parses_united_states_response()
    {
        $driver = (new class () extends UnitedStatesDriver {
            protected function fetch(string $url, array $query = []): ?string
            {
                return match ($query['series_id'] ?? '') {
                    'DEXUSEU' => file_get_contents(__DIR__.'/../Fixtures/united_states_eur.json'),
                    'DEXUSUK' => file_get_contents(__DIR__.'/../Fixtures/united_states_gbp.json'),
                    default => null,
                };
            }
        })->setDataTime(new DateTimeImmutable('2021-09-24'));

        $driver->grabExchangeRates();
        $data = $driver->retrieveData();

        $eur = collect($data)->firstWhere('code', 'EUR');
        $gbp = collect($data)->firstWhere('code', 'GBP');

        $this->assertNotNull($eur);
        $this->assertSame('EUR', $eur->code);
        $this->assertEquals(1, $eur->multiplier);
        $this->assertEquals(0.5, $eur->rate);

        $this->assertNotNull($gbp);
        $this->assertSame('GBP', $gbp->code);
        $this->assertEquals(1, $gbp->multiplier);
        $this->assertEquals(0.25, $gbp->rate);
    }
}
