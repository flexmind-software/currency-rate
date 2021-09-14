<?php

namespace FlexMindSoftware\CurrencyRate\Commands;

use Carbon\Carbon;
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
        $timestamp = ! blank($currencyDate) ? strtotime($currencyDate) : time();

        $driver = $this->option('driver');
        if ($driver === 'default') {
            $driver = config('currency-rate.driver');
        }

        if (! in_array($driver, array_keys(config('currency-rate.drivers')))) {
            $this->error('Driver "'.$driver.'" not exists!');

            return;
        }

        \CurrencyRate::driver($driver)->downloadRates(Carbon::createFromTimestamp($timestamp));
    }
}
