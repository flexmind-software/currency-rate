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
            if ($queue === 'none') {
                $this->processDriver($driver, $date, $connection);
            } else {
                $this->queueDriver($driver, $date, $connection, $queue);
            }
        }
    }

    private function processDriver(string $driver, DateTime $date, ?string $connection): void
    {
        try {
            $data = \CurrencyRate::driver($driver)
                ->setDataTime($date)
                ->grabExchangeRates()
                ->retrieveData();

            if ($data && $connection) {
                $this->saveInDatabase($data, $connection);
            }
        } catch (Throwable $exception) {
            Log::error(
                'Can\t grab data from [' . $driver . ']: ' . $exception->getMessage(),
                $exception->getTrace()
            );
        }
    }

    private function queueDriver(string $driver, DateTime $date, ?string $connection, string $queue): void
    {
        QueueDownload::dispatch($driver, $date, $connection)->onQueue($queue);
    }

    private function getAllDrivers(): array
    {
        $drivers = config('currency-rate.drivers', []);
        sort($drivers);

        return $drivers;
    }

    /**
     * @param array $data
     * @param string|null $connection
     */
    private function saveInDatabase(array $data, ?string $connection): void
    {
        if ($data) {
            CurrencyRate::saveIn($data, $connection);
        }
    }
}
