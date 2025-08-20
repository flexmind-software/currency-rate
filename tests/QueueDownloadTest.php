<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTime;
use FlexMindSoftware\CurrencyRate\CurrencyRateFacade as CurrencyRate;
use FlexMindSoftware\CurrencyRate\Jobs\QueueDownload;
use FlexMindSoftware\CurrencyRate\Tests\Stubs\FakeDriver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QueueDownloadTest extends TestCase
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
    public function it_handles_job_without_errors()
    {
        Http::fake([
            'example.com/*' => Http::response('ok', 200),
        ]);

        $job = new QueueDownload(FakeDriver::DRIVER_NAME, new DateTime('2023-10-01'), 'testing');
        $job->handle();

        $this->assertTrue(true);
    }

    /** @test */
    public function handle_logs_exception()
    {
        Log::shouldReceive('error')->once();

        $job = new QueueDownload('missing', new DateTime(), 'testing');
        $job->handle();
        $this->assertTrue(true);
    }
}
