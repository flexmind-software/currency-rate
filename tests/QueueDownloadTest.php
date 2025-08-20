<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Jobs\QueueDownload;
use FlexMindSoftware\CurrencyRate\Tests\Stubs\FakeDriver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QueueDownloadTest extends TestCase
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
    public function it_handles_job_without_errors()
    {
        Http::fake([
            'example.com/*' => Http::response('ok', 200),
        ]);

        $job = new QueueDownload(FakeDriver::DRIVER_NAME, new DateTimeImmutable('2023-10-01'), 'testing');
        $job->handle();

        $this->assertTrue(true);
    }

    /**
     * @test
     * @return void
     */
    public function handle_logs_exception()
    {
        Log::shouldReceive('error')->once();

        $job = new QueueDownload('missing', new DateTimeImmutable(), 'testing');
        $job->handle();
        $this->assertTrue(true);
    }
}
