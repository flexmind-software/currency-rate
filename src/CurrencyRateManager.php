<?php

namespace FlexMindSoftware\CurrencyRate;

use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CurrencyRateManager extends Manager
{
    /**
     * Get a driver instance.
     *
     * @param string $driver
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function driver($driver = null)
    {
        $driver = $this->createDriver(
            $driver ?: $this->getDefaultDriver()
        );
        if ($driver) {
            return $driver;
        }

        throw new InvalidArgumentException(sprintf(
            'Unable to resolve NULL driver for [%s].',
            static::class
        ));
    }

    /**
     * Create a new driver instance.
     *
     * @param string $driver
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        // First, we will determine if a custom driver creator exists for the given driver and
        // if it does not we will check for a creator method for the driver. Custom creator
        // callbacks allow developers to build their own "drivers" easily using Closures.
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } else {
            $class = 'FlexMindSoftware\\CurrencyRate\\Drivers\\' . Str::studly($driver) . 'Driver';
            if (class_exists($class)) {
                return $this->getContainer()->make($class);
            }
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return config('currency-rate.driver') ?? 'european-central-bank';
    }
}
