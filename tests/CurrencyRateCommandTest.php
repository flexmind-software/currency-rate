<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Jobs\QueueDownload;
use FlexMindSoftware\CurrencyRate\Tests\Stubs\FakeDriver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

class CurrencyRateCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
        CurrencyRate::extend('fake', fn () => new FakeDriver());
    }

    /**
     * @test
     * @return void
     */
    public function it_processes_driver_without_errors()
    {
        Http::fake([
            'example.com/*' => Http::response('ok', 200),
        ]);

        $this->artisan('flexmind:currency-rate', [
            'date' => '2023-10-01',
            '--driver' => FakeDriver::DRIVER_NAME,
            '--queue' => 'none',
            '--connection' => 'testing',
        ])->assertExitCode(0);
    }

    /**
     * @test
     * @return void
     */
    public function it_dispatches_job_when_queue_is_not_none()
    {
        Queue::fake();

        $this->artisan('flexmind:currency-rate', [
            'date' => '2023-10-01',
            '--driver' => FakeDriver::DRIVER_NAME,
            '--queue' => 'default',
            '--connection' => 'testing',
        ]);

        Queue::assertPushed(QueueDownload::class, function ($job) {
            return $job->uniqueId() !== '';
        });
    }
}
