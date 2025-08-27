<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Support\Logger;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class LoggerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_sends_logs_to_the_configured_channel()
    {
        config(['currency-rate.log_channel' => 'custom']);

        $mock = \Mockery::mock(LoggerInterface::class);
        $mock->shouldReceive('error')->once()->with('test');

        Log::shouldReceive('channel')->once()->with('custom')->andReturn($mock);

        Logger::error('test');
    }
}
