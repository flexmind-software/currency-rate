<?php

namespace FlexMindSoftware\CurrencyRate\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class CurrencyRateCommand extends Command
{
    public $signature = 'flexmind:currency-rate';

    public $description = 'Download currency rate';

    public function handle()
    {
        $currencyDate = $this->argument('currencyDate');
        $timestamp = ! blank($currencyDate) ? strtotime($currencyDate) : time();

        \CurrencyRate::driver('nbp')->downloadRates(Carbon::createFromTimestamp($timestamp));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['currencyDate', InputArgument::OPTIONAL, 'Date'],
        ];
    }
}
