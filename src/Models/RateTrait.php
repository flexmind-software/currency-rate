<?php

namespace FlexMindSoftware\CurrencyRate\Models;

use DateTime;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;

trait RateTrait
{
    public function rate(CurrencyCode|string $currencyFrom, CurrencyCode|string $currencyTo, DateTime $date)
    {
        $currencyFrom = $currencyFrom instanceof CurrencyCode ? $currencyFrom->value : $currencyFrom;
        $currencyTo = $currencyTo instanceof CurrencyCode ? $currencyTo->value : $currencyTo;
        $baseCurrency = $this->currency instanceof CurrencyCode ? $this->currency->value : $this->currency;

        $rates = $this->retrieveDataToCalculation($currencyFrom, $currencyTo, $date);

        $from = $currencyFrom == $baseCurrency ? 1 : ($rates['from'] ?? null);
        $to = $currencyTo == $baseCurrency ? 1 : ($rates['to'] ?? null);

        if ($from && $to) {
            return $to ? $from / $to : 0;
        }

        return 0;
    }

    /**
     * @param CurrencyCode|string $currencyFrom
     * @param CurrencyCode|string $currencyTo
     * @param DateTime $date
     *
     * @return array
     */
    public function retrieveDataToCalculation(CurrencyCode|string $currencyFrom, CurrencyCode|string $currencyTo, DateTime $date): array
    {
        $currencyFrom = $currencyFrom instanceof CurrencyCode ? $currencyFrom->value : $currencyFrom;
        $currencyTo = $currencyTo instanceof CurrencyCode ? $currencyTo->value : $currencyTo;

        $row = CurrencyRate::where('driver', static::DRIVER_NAME)
            ->whereDate('date', $date->format('Y-m-d'))
            ->whereIn('code', [$currencyFrom, $currencyTo])
            ->get()
            ->pluck('calculate_rate', 'code');

        return [
            'from' => $row->get($currencyFrom),
            'to' => $row->get($currencyTo),
        ];
    }
}
