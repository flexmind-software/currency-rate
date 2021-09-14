<?php

namespace FlexMindSoftware\CurrencyRate\Commands;

use Illuminate\Console\Command;

class CurrencyRateCommand extends Command
{
    public $signature = 'flexmind:currency-rate';

    public $description = '';

    public function handle()
    {
        $this->comment('All done');
    }
}
