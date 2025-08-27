<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Drivers\HttpFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;

class HttpFetcherRetryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        config()->set('currency-rate.cache-ttl', 3600);
        config()->set('currency-rate.retry.count', 3);
        config()->set('currency-rate.retry.sleep', 0);
        config()->set('currency-rate.retry.factor', 1);
    }

    /**
     * @test
     */
    public function fetch_retries_after_timeout_and_succeeds(): void
    {
        $attempts = 0;
        $mock = new MockHandler([
            function (Request $request) use (&$attempts) {
                $attempts++;

                throw new ConnectException('timeout', $request);
            },
            function (Request $request) use (&$attempts) {
                $attempts++;

                throw new ConnectException('timeout', $request);
            },
            function (Request $request) use (&$attempts) {
                $attempts++;

                return new Response(200, [], 'ok');
            },
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $fetcher = new class ($client) {
            use HttpFetcher;

            public function callFetch(string $url, array $query = [])
            {
                return $this->fetch($url, $query);
            }
        };

        $this->assertSame('ok', $fetcher->callFetch('https://example.com/test'));
        $this->assertSame(3, $attempts);
    }
}
