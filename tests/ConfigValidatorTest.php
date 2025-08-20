<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\CurrencyRateServiceProvider;

class ConfigValidatorTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [];
    }

    /** @test */
    public function it_throws_exception_when_required_config_missing()
    {
        $this->app['config']->set('currency-rate', []);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('currency-rate configuration missing required key [driver].');

        $provider = new CurrencyRateServiceProvider($this->app);
        $provider->packageRegistered();
    }

    /** @test */
    public function it_passes_when_required_config_present()
    {
        $this->app['config']->set('currency-rate', [
            'driver' => 'european-central-bank',
            'drivers' => ['european-central-bank'],
            'table-name' => 'currency_rates',
        ]);

        $provider = new CurrencyRateServiceProvider($this->app);
        $provider->packageRegistered();

        $this->assertTrue($this->app->bound('currency-rate'));
    }
}

