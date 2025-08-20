<?php

namespace FlexMindSoftware\CurrencyRate\Commands;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Jobs\QueueDownload;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate as CurrencyRateModel;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
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

    /**
     * @return int
     */
    public function handle(): int
    {
        $currencyDate = $this->argument('date');
        $timestamp = is_string($currencyDate) && $currencyDate !== '' ? strtotime($currencyDate) ?: time() : time();

        $queueOption = $this->option('queue');
        $queue = is_string($queueOption) ? $queueOption : 'none';
        $driverOption = $this->option('driver');
        $driver = $driverOption ?? 'all';
        $connectionOption = $this->option('connection');
        $connection = is_string($connectionOption) ? $connectionOption : 'default';

        if ($driver === 'all') {
            $driver = $this->getAllDrivers();
        } elseif ($driver === 'default') {
            $driver = Arr::wrap(config('currency-rate.driver'));
        }

        $drivers = Arr::wrap($driver);
        $drivers = array_filter($drivers);
        $date = new DateTimeImmutable("@$timestamp");

        if ($queue === 'none' && count($drivers) > 1) {
            $jobs = [];
            foreach ($drivers as $driver) {
                $jobs[] = new QueueDownload($driver, clone $date, $connection);
            }

            Bus::batch($jobs)->dispatch();
        } else {
            foreach ($drivers as $driver) {
                if ($queue === 'none') {
                    $this->processDriver($driver, $date, $connection);
                } else {
                    $this->queueDriver($driver, $date, $connection, $queue);
                }
            }
        }

        return Command::SUCCESS;
    }

    private function processDriver(string $driver, DateTimeImmutable $date, ?string $connection): void
    {
        try {
            $data = CurrencyRate::driver($driver)
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

    private function queueDriver(string $driver, DateTimeImmutable $date, ?string $connection, string $queue): void
    {
        QueueDownload::dispatch($driver, $date, $connection)->onQueue($queue);
    }

    /**
     * @return array<int, string>
     */
    private function getAllDrivers(): array
    {
        $drivers = config('currency-rate.drivers', []);
        sort($drivers);

        return $drivers;
    }

    /**
     * @param array<int, mixed> $data
     */
    private function saveInDatabase(array $data, string $connection): void
    {
        if ($data) {
            CurrencyRateModel::saveIn($data, $connection);
        }
    }
}
