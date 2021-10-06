<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\CurrencyRateServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

class AbstractTestCase extends AbstractPackageTestCase
{
    /**
     * Get the service provider class.
     *
     * @return string
     */
    protected function getServiceProviderClass()
    {
        return CurrencyRateServiceProvider::class;
    }
}
