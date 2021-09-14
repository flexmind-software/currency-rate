<?php

namespace FlexMindSoftware\CurrencyRate\Models;

trait RateTrait
{
    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param \DateTime $date
     *
     * @return array
     */
    public function retrieveDataToCalculation(string $currencyFrom, string $currencyTo, \DateTime $date): array
    {
        $row = CurrencyRate::where(
            [
                'driver' => $this->driverAlias,
                'date' => $date->format('Y-m-d'),
            ]
        )->where(function ($builder) use ($currencyFrom, $currencyTo) {
            $builder->whereIn('code', [$currencyFrom, $currencyTo]);
        })
            ->select(['code', 'rate', 'multiplier'])
            ->get();


        $from = $row->where('code', '=', $currencyFrom)->first();
        $to = $row->where('code', '=', $currencyTo)->first();

        return [$from, $to];
    }

    public function rate(string $currencyFrom, string $currencyTo, \DateTime $date)
    {
        [$from, $to] = $this->retrieveDataToCalculation($currencyFrom, $currencyTo, $date);
        if ($from && $to) {
            $from = $currencyFrom == $this->currency ? 1 : $from->calculate_rate;
            $to = $currencyTo == $this->currency ? 1 : $to->calculate_rate;
            return $to ? $from / $to : 0;
        }

        return 0;
    }
}
