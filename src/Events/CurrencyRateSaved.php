<?php

namespace FlexMindSoftware\CurrencyRate\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CurrencyRateSaved
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var array<int, array|string|float|null>
     */
    public array $rates;

    /**
     * @param array<int, array|string|float|null> $rates
     */
    public function __construct(array $rates)
    {
        $this->rates = $rates;
    }
}
