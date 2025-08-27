<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class UnitedStatesDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    public const URI = 'https://api.stlouisfed.org/fred/series/observations';

    public const DRIVER_NAME = 'united-states';

    /**
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::USD;

    /**
     * Map ISO currency codes to FRED series identifiers.
     *
     * @var array<string, string>
     */
    protected const SERIES_MAP = [
        'EUR' => 'DEXUSEU',
        'GBP' => 'DEXUSUK',
    ];

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $this->data = [];
        $date = $this->date->format('Y-m-d');

        foreach (self::SERIES_MAP as $code => $series) {
            $query = array_filter([
                'series_id' => $series,
                'observation_start' => $date,
                'observation_end' => $date,
                'file_type' => 'json',
                'api_key' => $this->config['fed']['api_key'] ?? null,
            ]);

            $response = $this->fetch(self::URI, $query);
            if ($response) {
                $json = json_decode($response, true);
                $value = $json['observations'][0]['value'] ?? null;

                if ($value && $value !== '.') {
                    $rate = (float) $value;
                    if ($rate > 0) {
                        $this->data[] = [
                            'no' => null,
                            'code' => $code,
                            'driver' => self::DRIVER_NAME,
                            'date' => $date,
                            'multiplier' => 1,
                            'rate' => 1 / $rate,
                        ];
                    }
                }
            }
        }

        return $this;
    }

    public function fullName(): string
    {
        return 'Federal Reserve Bank of St. Louis';
    }

    public function homeUrl(): string
    {
        return 'https://fred.stlouisfed.org/';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.united-states.frequency');
    }
}
