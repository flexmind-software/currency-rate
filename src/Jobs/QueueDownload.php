<?php

namespace FlexMindSoftware\CurrencyRate\Jobs;

use DateTime;
use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate as CurrencyRateModel;
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
     * @var DateTime
     */
    protected DateTime $dateTime;
    /**
     * @var string
     */
    protected string $databaseConnection;

    /**
     * @param string $driverName
     * @param DateTime $dateTime
     * @param string $connection
     */
    public function __construct(string $driverName, DateTime $dateTime, string $connection)
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
