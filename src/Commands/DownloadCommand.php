<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Commands;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Jobs\QueueDownload;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate as CurrencyRateModel;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * php artisan flexmind:currency-rate:download --driver=
 */
class DownloadCommand extends Command
{
    public $signature = 'flexmind:currency-rate:download
        {date? : Date to download currency rate, if empty is today}
        {--queue=none : Queue name, if set "none" cmd run without add job to queue};
        {--connection=default : The database connection to use};
        {--driver=all : Driver to download rate};
        {--currency= : Comma separated list of currency codes to save}';

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

        $currencyOption = $this->option('currency');
        $currencies = [];
        if (is_array($currencyOption)) {
            $currencies = $currencyOption;
        } elseif (is_string($currencyOption) && $currencyOption !== '') {
            $currencies = array_map('trim', explode(',', $currencyOption));
        }
        $currencies = array_filter($currencies);

        if ($driver === 'all') {
            $driver = $this->getAllDrivers();
        } elseif ($driver === 'default') {
            $driver = Arr::wrap(config('currency-rate.driver'));
        }

        $drivers = Arr::wrap($driver);
        $drivers = array_filter($drivers);
        $date = new DateTimeImmutable("@$timestamp");

        foreach ($drivers as $driver) {
            if ($queue === 'none') {
                $this->processDriver($driver, $date, $connection, $currencies);
            } else {
                $this->queueDriver($driver, $date, $connection, $queue);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param string $driver
     * @param DateTimeImmutable $date
     * @param string|null $connection
     * @param array<int, string> $currencies
     */
    private function processDriver(string $driver, DateTimeImmutable $date, ?string $connection, array $currencies): void
    {
        try {
            $data = CurrencyRate::driver($driver)
                ->setDataTime($date)
                ->grabExchangeRates()
                ->retrieveData();

            if ($currencies !== []) {
                $data = array_filter($data, fn ($item) => in_array($item->code, $currencies, true));
            }

            if ($data && $connection) {
                $this->saveInDatabase($data, $connection);
            }
        } catch (Throwable $exception) {
            Log::error(
                'Can\\t grab data from [' . $driver . ']: ' . $exception->getMessage(),
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
