<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\CurrencyRateServiceProvider;
use InvalidArgumentException;

class DriverValidationTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [];
    }

    /**
     * @test
     */
    public function it_throws_exception_for_missing_driver_class(): void
    {
        $this->app['config']->set('currency-rate', [
            'driver' => 'non-existing-driver',
            'drivers' => ['non-existing-driver'],
            'table-name' => 'currency_rates',
        ]);

        $provider = new CurrencyRateServiceProvider($this->app);
        $provider->packageRegistered();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Driver class [FlexMindSoftware\\CurrencyRate\\Drivers\\NonExistingDriverDriver] for driver [non-existing-driver] does not exist.');

        $provider->packageBooted();
    }
}
