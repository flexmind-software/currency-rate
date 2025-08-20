<?php
declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Contracts\DriverMetadata;
use FlexMindSoftware\CurrencyRate\Tests\Stubs\FakeDriver;

class DriverMetadataTest extends TestCase
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
    public function it_exposes_driver_name_and_uri()
    {
        $driver = new FakeDriver();

        $this->assertInstanceOf(DriverMetadata::class, $driver);
        $this->assertSame(FakeDriver::DRIVER_NAME, $driver->driverName());
        $this->assertSame(FakeDriver::URI, $driver->uri());
    }
}
