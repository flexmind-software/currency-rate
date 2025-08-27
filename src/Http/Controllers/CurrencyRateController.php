<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Http\Controllers;

use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use Illuminate\Http\JsonResponse;

class CurrencyRateController
{
    public function show(string $code): JsonResponse
    {
        $rate = CurrencyRate::query()
            ->where('code', strtoupper($code))
            ->orderByDesc('date')
            ->first();

        if (! $rate) {
            abort(404);
        }

        return response()->json([
            'value' => $rate->calculate_rate,
            'date' => $rate->date->toDateString(),
        ]);
    }
}
