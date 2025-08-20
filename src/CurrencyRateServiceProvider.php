<?php

namespace FlexMindSoftware\CurrencyRate;

use FlexMindSoftware\CurrencyRate\Commands\CurrencyRateCommand;
use InvalidArgumentException;
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
            ->hasTranslations()
            ->hasMigration('create_currency_rate_table')
            ->hasCommand(CurrencyRateCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->validateConfig();

        $this->app->singleton('currency-rate', function ($app) {
            return new CurrencyRateManager($app);
        });
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
}
