<?php

namespace FlexMindSoftware\CurrencyRate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     * @var \DateTime
     */
    protected \DateTime $dateTime;

    /**
     * @param string $driverName
     * @param \DateTime $dateTime
     */
    public function __construct(string $driverName, \DateTime $dateTime)
    {
        $this->driverName = $driverName;
        $this->dateTime = $dateTime;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return sprintf('%s_%s_%s', 'flexmind', $this->driverName, $this->dateTime->format('Y_m_d'));
    }

    public function handle()
    {
        \CurrencyRate::driver($this->driverName)->downloadRates($this->dateTime);
    }
}
