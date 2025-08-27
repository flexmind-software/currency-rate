<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

class LangEsTest extends TestCase
{
    /** @test */
    public function it_loads_spanish_translations(): void
    {
        app()->setLocale('es');

        $this->assertSame(
            'Diariamente en días laborables',
            __('currency-rate::description.albania.frequency')
        );
    }
}
