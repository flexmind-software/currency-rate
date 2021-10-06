<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\CurrencyRateManager;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    /**
     * @test
     *
     * @group CurrencyRate
     */
    public function currencyRateManagerIsInjectable()
    {
        $this->assertIsInjectable(CurrencyRateManager::class);
    }
}
