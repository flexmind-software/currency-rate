<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use DateTime;
use FlexMindSoftware\CurrencyRate\Jobs\QueueDownload;
use Illuminate\Support\Facades\Log;

class QueueDownloadTest extends TestCase
{
    /** @test */
    public function handle_logs_exception()
    {
        Log::shouldReceive('error')->once();

        $job = new QueueDownload('fake', new DateTime(), 'testing');

        $job->handle();
        $this->assertTrue(true);
    }
}
