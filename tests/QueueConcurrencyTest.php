<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Jobs\QueueDownload;
use FlexMindSoftware\CurrencyRate\Tests\Stubs\FakeDriver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class QueueConcurrencyTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
        CurrencyRate::extend('fake', fn () => new FakeDriver());
    }

    /** @test */
    public function job_is_released_when_concurrency_limit_is_reached(): void
    {
        config()->set('currency-rate.queue_concurrency', 1);
        Http::fake(['example.com/*' => Http::response('ok', 200)]);

        $funnel = new class () {
            public function limit($limit)
            {
                return $this;
            }

            public function block($seconds)
            {
                return $this;
            }

            public function then($success, $failure)
            {
                $failure();
            }
        };

        $redis = new class ($funnel) {
            public function __construct(private $funnel)
            {
            }

            public function funnel($name)
            {
                return $this->funnel;
            }
        };

        Redis::swap($redis);

        $job = $this->getMockBuilder(QueueDownload::class)
            ->setConstructorArgs([FakeDriver::DRIVER_NAME, new DateTimeImmutable('2023-10-01'), 'testing'])
            ->onlyMethods(['release'])
            ->getMock();

        $job->expects($this->once())->method('release')->with(10);

        $job->handle();
    }
}
