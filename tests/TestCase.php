<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\CurrencyRateServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Redis;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'VendorName\\Skeleton\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $funnel = new class () {
            public function limit($limit)
            {
                return $this;
            }

            public function block($seconds)
            {
                return $this;
            }

            public function then($success, $failure)
            {
                $success();
            }
        };

        $redis = new class ($funnel) {
            public function __construct(private $funnel)
            {
            }

            public function funnel($name)
            {
                return $this->funnel;
            }
        };

        Redis::swap($redis);
    }

    protected function getPackageProviders($app)
    {
        return [
            CurrencyRateServiceProvider::class,
        ];
    }

    /**
     * @param mixed $app
     * @return void
     */
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => __DIR__.'/../database/database.sqlite',
            'prefix' => '',
        ]);
    }
}
