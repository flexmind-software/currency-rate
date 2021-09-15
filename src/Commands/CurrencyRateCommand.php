<?php

namespace FlexMindSoftware\CurrencyRate\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CurrencyRateCommand extends Command
{
    public $signature = 'flexmind:currency-rate
        {date? : Date to download currency rate, if empty is today}
        {--driver=all : Driver to download rate}';

    public $description = 'Download and save into database currency rates from different national bank';

    public function handle()
    {
        $currencyDate = $this->argument('date');
        $timestamp = ! blank($currencyDate) ? strtotime($currencyDate) : time();

        $driver = $this->option('driver');

        if ($driver === 'all') {
            $driver = $this->getAllDrivers();
        } elseif ($driver === 'default') {
            $driver = Arr::wrap(config('currency-rate.driver'));
        }

        $drivers = Arr::wrap($driver);
        $date = new DateTime("@$timestamp");
        foreach ($drivers as $driver) {
            \CurrencyRate::driver($driver)->downloadRates($date);
        }
    }

    private function getAllDrivers(): array
    {
        $drivers = glob(__DIR__ . '/../Drivers/*Driver.php');
        $drivers = array_filter($drivers, function ($item) {
            return strpos($item, 'BaseDriver') === false;
        });

        $drivers = array_map(function ($item) {
            $baseName = basename($item, '.php');
            $className = 'FlexMindSoftware\\CurrencyRate\\Drivers\\' . $baseName;

            return $className::DRIVER_NAME;
        }, $drivers);

        sort($drivers);

        return $drivers;
    }
}
