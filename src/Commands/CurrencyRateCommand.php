<?php

namespace FlexMindSoftware\CurrencyRate\Commands;

use DateTime;
use FlexMindSoftware\CurrencyRate\Jobs\QueueDownload;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * php artisan flexmind:currency-rate --driver=
 */
class CurrencyRateCommand extends Command
{
    public $signature = 'flexmind:currency-rate
        {date? : Date to download currency rate, if empty is today}
        {--queue=none : Queue name, if set "none" cmd run without add job to queue};
        {--connection=default : The database connection to use};
        {--driver=all : Driver to download rate}';

    public $description = 'Download and save into database currency rates from different national bank';

    public function handle()
    {
        $currencyDate = $this->argument('date');
        $timestamp = ! blank($currencyDate) ? strtotime($currencyDate) : time();

        $queue = $this->option('queue');
        $driver = $this->option('driver');
        $connection = $this->option('connection');

        if ($driver === 'all') {
            $driver = $this->getAllDrivers();
        } elseif ($driver === 'default') {
            $driver = Arr::wrap(config('currency-rate.driver'));
        }

        $drivers = Arr::wrap($driver);
        $drivers = array_filter($drivers);
        $date = new DateTime("@$timestamp");
        foreach ($drivers as $driver) {
            if ($queue == 'none') {
                try {
                    $data = \CurrencyRate::driver($driver)
                        ->setDataTime($date)
                        ->grabExchangeRates()
                        ->retrieveData();

                    if ($data && $connection) {
                        $this->saveInDatabase($data);
                    }
                } catch (Throwable $exception) {
                    Log::error(
                        'Can\t grab data from [' . $driver . ']: ' . $exception->getMessage(),
                        $exception->getTrace()
                    );
                }
            } else {
                QueueDownload::dispatch($driver, $date, $connection)->onQueue($queue);
            }
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

    /**
     * @param array $data
     */
    private function saveInDatabase(array $data)
    {
        if ($data) {
            $connection = $this->option('connection');
            CurrencyRate::saveIn($data, $connection);
        }
    }
}
