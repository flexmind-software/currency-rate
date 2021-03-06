<?php

namespace FlexMindSoftware\CurrencyRate;

use FlexMindSoftware\CurrencyRate\Commands\CurrencyRateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CurrencyRateServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('currency-rate')
            ->hasConfigFile()
            ->hasMigration('create_currency_rate_table')
            ->hasCommand(CurrencyRateCommand::class);
    }

    public function packageRegistered()
    {
        $this->app->singleton('currency-rate', function ($app) {
            return new CurrencyRateManager($app);
        });
    }
}
