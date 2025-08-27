<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate;

use FlexMindSoftware\CurrencyRate\Commands\CurrencyRateCommand;
use FlexMindSoftware\CurrencyRate\Drivers\UnitedStatesDriver;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CurrencyRateServiceProvider extends PackageServiceProvider
{
    /**
     * @param Package $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        /*
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('currency-rate')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasRoute('api')
            ->hasMigration('create_currency_rate_table')
            ->hasCommand(CurrencyRateCommand::class);
    }

    /**
     * @return void
     */
    public function packageRegistered(): void
    {
        $this->validateConfig();

        $this->app->singleton('currency-rate', function ($app) {
            return new CurrencyRateManager($app);
        });

        $this->app->bind(UnitedStatesDriver::class, fn ($app) => new UnitedStatesDriver());
    }

    /**
     * @return void
     */
    public function packageBooted(): void
    {
        $this->validateDrivers();
    }

    protected function validateConfig(): void
    {
        $required = ['driver', 'table-name', 'drivers'];

        foreach ($required as $key) {
            if (! config()->has("currency-rate.$key") || empty(config("currency-rate.$key"))) {
                throw new InvalidArgumentException(
                    "currency-rate configuration missing required key [$key]."
                );
            }
        }
    }

    protected function validateDrivers(): void
    {
        foreach (config('currency-rate.drivers', []) as $driver) {
            $class = 'FlexMindSoftware\\CurrencyRate\\Drivers\\'.Str::studly($driver).'Driver';

            if (! class_exists($class)) {
                throw new InvalidArgumentException(
                    "Driver class [$class] for driver [$driver] does not exist."
                );
            }
        }
    }
}
