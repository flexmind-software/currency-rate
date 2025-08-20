<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Jobs;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate as CurrencyRateModel;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class QueueDownload implements ShouldQueue, ShouldBeUnique, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * 3h
     *
     * @var int
     */
    public int $uniqueFor = 10800;
    /**
     * @var string
     */
    protected string $driverName;
    /**
     * @var DateTimeImmutable
     */
    protected DateTimeImmutable $dateTime;
    /**
     * @var string
     */
    protected string $databaseConnection;

    /**
     * @param string $driverName
     * @param DateTimeImmutable $dateTime
     * @param string $connection
     */
    public function __construct(string $driverName, DateTimeImmutable $dateTime, string $connection)
    {
        $this->driverName = $driverName;
        $this->dateTime = $dateTime;
        $this->databaseConnection = $connection;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        $prefix = config('app.name', 'currency-rate');

        return sprintf('%s_%s_%s', $prefix, $this->driverName, $this->dateTime->format('Y_m_d'));
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            $data = CurrencyRate::driver($this->driverName)
                ->setDataTime($this->dateTime)
                ->grabExchangeRates()
                ->retrieveData();

            if ($data && $this->databaseConnection) {
                CurrencyRateModel::saveIn($data, $this->databaseConnection);
            }
        } catch (Throwable $exception) {
            Log::error(
                'QueueDownload job failed: ' . $exception->getMessage(),
                $exception->getTrace()
            );
        }
    }
}
