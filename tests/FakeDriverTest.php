<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Tests\Stubs\FakeDriver;
use Illuminate\Support\Facades\Http;

class FakeDriverTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
    }

    /**
     * @test
     * @return void
     */
    public function it_fetches_and_parses_rates()
    {
        Http::fake([
            'example.com/*' => Http::response('ok', 200),
        ]);

        $driver = new FakeDriver();
        $driver->grabExchangeRates();

        $data = $driver->retrieveData();

        $this->assertEquals('USD', $data[0]->code);
        $this->assertEquals(1.1, $data[0]->rate);
    }
}
