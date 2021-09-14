<?php

namespace FlexMindSoftware\CurrencyRate\Contracts;

use Carbon\Carbon;

interface CurrencyInterface
{
    public function downloadRates(Carbon $date);
}
