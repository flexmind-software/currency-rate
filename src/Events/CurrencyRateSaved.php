<?php

namespace FlexMindSoftware\CurrencyRate\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CurrencyRateSaved
{
    use Dispatchable;
    use SerializesModels;

    public array $rates;

    public function __construct(array $rates)
    {
        $this->rates = $rates;
    }
}
