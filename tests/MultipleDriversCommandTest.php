<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Jobs\QueueDownload;
use FlexMindSoftware\CurrencyRate\Tests\Stubs\FakeDriver;
use FlexMindSoftware\CurrencyRate\Tests\Stubs\SecondFakeDriver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;

class MultipleDriversCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $migration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $migration->up();
        $batchMigrationClass = 'TestbenchCreateJobBatchesTable';
        if (!class_exists($batchMigrationClass)) {
            include __DIR__.'/../vendor/orchestra/testbench-core/laravel/migrations/queue/0001_01_01_000000_testbench_create_job_batches_table.php';
        }
        (new $batchMigrationClass())->up();
        CurrencyRate::extend(FakeDriver::DRIVER_NAME, fn () => new FakeDriver());
        CurrencyRate::extend(SecondFakeDriver::DRIVER_NAME, fn () => new SecondFakeDriver());
        config(['currency-rate.drivers' => [FakeDriver::DRIVER_NAME, SecondFakeDriver::DRIVER_NAME]]);
    }

    /** @test */
    public function it_dispatches_jobs_for_all_drivers()
    {
        Queue::fake();

        $this->artisan('flexmind:currency-rate', [
            'date' => '2023-10-01',
            '--driver' => 'all',
            '--queue' => 'default',
            '--connection' => 'testing',
        ]);

        Queue::assertPushed(QueueDownload::class, 2);
        Queue::assertPushed(QueueDownload::class, function ($job) {
            return str_contains($job->uniqueId(), FakeDriver::DRIVER_NAME);
        });
        Queue::assertPushed(QueueDownload::class, function ($job) {
            return str_contains($job->uniqueId(), SecondFakeDriver::DRIVER_NAME);
        });
    }

    /** @test */
    public function it_saves_rates_from_all_drivers()
    {
        Http::fake([
            'example.com/*' => Http::response('ok', 200),
        ]);

        $this->artisan('flexmind:currency-rate', [
            'date' => '2023-10-01',
            '--driver' => 'all',
            '--queue' => 'none',
            '--connection' => 'testing',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('currency_rates', ['code' => 'USD']);
        $this->assertDatabaseHas('currency_rates', ['code' => 'GBP']);
    }

    /** @test */
    public function it_batches_jobs_when_no_queue_and_multiple_drivers()
    {
        Bus::fake();

        $this->artisan('flexmind:currency-rate', [
            'date' => '2023-10-01',
            '--driver' => 'all',
            '--queue' => 'none',
            '--connection' => 'testing',
        ]);

        Bus::assertBatchCount(1);
        Bus::assertBatched(function ($batch) {
            return count($batch->jobs) === 2;
        });
    }
}
