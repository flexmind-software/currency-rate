<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\CurrencyRateServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            function (string $modelName) {
                return 'VendorName\\Skeleton\\Database\\Factories\\' . class_basename($modelName) . 'Factory';
            }
        );
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_skeleton_table.php.stub';
        $migration->up();
        */
    }

    protected function getPackageProviders($app)
    {
        return [
            CurrencyRateServiceProvider::class,
        ];
    }
}
