<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Drivers\HttpFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;

class HttpFetcherCacheTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        config()->set('currency-rate.cache-ttl', 3600);
        config()->set('currency-rate.retry.count', 1);
        config()->set('currency-rate.retry.sleep', 0);
        config()->set('currency-rate.retry.factor', 1);
    }

    /**
     * @test
     */
    public function fetch_returns_cached_response_on_subsequent_calls(): void
    {
        $attempts = 0;
        $mock = new MockHandler([
            function (Request $request) use (&$attempts) {
                $attempts++;
                return new Response(200, [], 'content');
            },
            function (Request $request) use (&$attempts) {
                $attempts++;
                return new Response(200, [], 'new-content');
            },
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $fetcher = new class($client) {
            use HttpFetcher;
            public function callFetch(string $url, array $query = [])
            {
                return $this->fetch($url, $query);
            }
        };

        $this->assertSame('content', $fetcher->callFetch('https://example.com/test', ['a' => 1]));
        $this->assertSame('content', $fetcher->callFetch('https://example.com/test', ['a' => 1]));
        $this->assertSame(1, $attempts);
    }
}
