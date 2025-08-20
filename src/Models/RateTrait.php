<?php

namespace FlexMindSoftware\CurrencyRate\Models;

use DateTime;

trait RateTrait
{
    public function rate(string $currencyFrom, string $currencyTo, DateTime $date)
    {
        $rates = $this->retrieveDataToCalculation($currencyFrom, $currencyTo, $date);

        $from = $currencyFrom == $this->currency ? 1 : ($rates['from'] ?? null);
        $to = $currencyTo == $this->currency ? 1 : ($rates['to'] ?? null);

        if ($from && $to) {
            return $to ? $from / $to : 0;
        }

        return 0;
    }

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param DateTime $date
     *
     * @return array
     */
    public function retrieveDataToCalculation(string $currencyFrom, string $currencyTo, DateTime $date): array
    {
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
