<?php
declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Models;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;

trait RateTrait
{
    /**
     * Cache of rates retrieved within a single request.
     *
     * @var array<string, array{from: float|null, to: float|null}>
     */
    private static array $rateCache = [];

    /**
     * @param CurrencyCode|string $currencyFrom
     * @param CurrencyCode|string $currencyTo
     * @param DateTimeImmutable $date
     * @return float|int
     */
    public function rate(CurrencyCode|string $currencyFrom, CurrencyCode|string $currencyTo, DateTimeImmutable $date)
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
     * @param DateTimeImmutable $date
     *
     * @return array{from: ?float, to: ?float}
     */
    public function retrieveDataToCalculation(CurrencyCode|string $currencyFrom, CurrencyCode|string $currencyTo, DateTimeImmutable $date): array
    {
        $currencyFrom = $currencyFrom instanceof CurrencyCode ? $currencyFrom->value : $currencyFrom;
        $currencyTo = $currencyTo instanceof CurrencyCode ? $currencyTo->value : $currencyTo;

        $cacheKey = implode('|', [
            static::DRIVER_NAME,
            $date->format('Y-m-d'),
            $currencyFrom,
            $currencyTo,
        ]);

        if (! array_key_exists($cacheKey, self::$rateCache)) {
            $row = CurrencyRate::where('driver', static::DRIVER_NAME)
                ->whereDate('date', $date->format('Y-m-d'))
                ->whereIn('code', [$currencyFrom, $currencyTo])
                ->get()
                ->pluck('calculate_rate', 'code');

            self::$rateCache[$cacheKey] = [
                'from' => $row->get($currencyFrom),
                'to' => $row->get($currencyTo),
            ];
        }

        return self::$rateCache[$cacheKey];
    }
}
