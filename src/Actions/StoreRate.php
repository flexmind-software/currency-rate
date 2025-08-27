<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Actions;

use FlexMindSoftware\CurrencyRate\Models\AuditLog;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use Illuminate\Support\Carbon;

class StoreRate
{
    public function execute(string $code, float $newRate): CurrencyRate
    {
        $currencyCode = strtoupper($code);
        $currencyRate = CurrencyRate::where('code', $currencyCode)->first();
        $oldRate = $currencyRate?->rate;

        AuditLog::create([
            'currency_code' => $currencyCode,
            'old_rate' => $oldRate,
            'new_rate' => $newRate,
            'changed_at' => Carbon::now(),
        ]);

        if ($currencyRate) {
            $currencyRate->rate = $newRate;
            $currencyRate->save();

            return $currencyRate;
        }

        return CurrencyRate::create([
            'driver' => 'manual',
            'code' => $currencyCode,
            'date' => Carbon::now()->toDateString(),
            'rate' => $newRate,
            'multiplier' => 1,
            'no' => null,
        ]);
    }
}
