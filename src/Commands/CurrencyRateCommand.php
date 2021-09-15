<?php

namespace FlexMindSoftware\CurrencyRate\Commands;

use Illuminate\Console\Command;

class CurrencyRateCommand extends Command
{
    public $signature = 'flexmind:currency-rate
        {date? : Date to download currency rate, if empty is today}
        {--driver=default : Driver to download rate}';

    public $description = 'Download currency rate';

    public function handle()
    {
        $currencyDate = $this->argument('date');
        $timestamp = !blank($currencyDate) ? strtotime($currencyDate) : time();

        $driver = $this->option('driver');
        if ($driver === 'default') {
            $driver = config('currency-rate.driver');
        }

        $date = new DateTime("@$timestamp");

        \CurrencyRate::driver($driver)->downloadRates($date);
    }
}
