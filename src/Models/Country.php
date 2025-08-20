<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Models;

use Exception;
use Illuminate\Support\Arr;

class Country
{
    /**
     * Cached list of countries.
     *
     * @var array
     */
    public static array $countries = [];

    /**
     * Indicates if countries have been loaded.
     *
     * @var bool
     */
    protected static bool $loaded = false;

    /**
     * Load countries from the JSON data file on first use.
     *
     * @throws Exception
     */
    public static function loadCountries(): void
    {
        if (self::$loaded) {
            return;
        }

        $path = __DIR__ . '/../../resources/data/countries.json';

        if (! is_readable($path)) {
            throw new Exception('Countries data file not found');
        }

        $data = json_decode(file_get_contents($path), true);

        if (! is_array($data)) {
            throw new Exception('Invalid countries data');
        }

        self::$countries = $data;
        self::$loaded = true;
    }

    /**
     * @param string $value
     * @param string $key
     * @return array<string, mixed>
     * @throws Exception
     */
    public static function getAllCountryList(string $value = 'name', string $key = 'iso2'): array
    {
        self::loadCountries();

        if (! in_array($value, ['iso3', 'name', 'capital', 'currency', 'phone'])) {
            throw new Exception('Value is not a valid field name');
        }

        if (! in_array($key, ['iso2', 'iso3', 'name', 'capital', 'currency', 'phone'])) {
            throw new Exception('Key is not a valid field name');
        }

        $countryList = [];

        foreach (self::$countries as $iso2 => $country) {
            if ($key == 'iso2') {
                $countryList[$iso2] = $country[$value];
            } elseif ($key == 'currency') {
                if (! isset($countryList[$country[$key]])) {
                    $countryList[$country[$key]] = [];
                }
                $countryList[$country[$key]][] = $country[$value];
            } else {
                $countryList[$country[$key]] = $country[$value];
            }
        }

        return $countryList;
    }

    /**
     * @param string $currency
     *
     * @return array
     */
    public static function getCountriesByCurrency(string $currency): array
    {
        self::loadCountries();

        return Arr::where(self::$countries, function ($item) use ($currency) {
            return $item['currency'] == $currency;
        });
    }
}
