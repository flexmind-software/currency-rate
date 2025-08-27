<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Drivers\HttpFetcher;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Support\Facades\Http;

class RedisCacheTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('currency-rate.cache_store', 'redis');
        config()->set('currency-rate.cache-ttl', 123);
    }

    private function fetcher()
    {
        return new class () {
            use HttpFetcher;

            public function callFetch($url, $query = [])
            {
                return $this->fetch($url, $query);
            }
        };
    }

    /** @test */
    public function it_caches_with_ttl_and_reads_from_redis(): void
    {
        $redis = new class implements RedisFactory {
            public array $calls = [];
            public array $data = [];

            public function connection($name = null)
            {
                return new class ($this) {
                    public function __construct(private $parent) {}

                    public function setex($key, $seconds, $value)
                    {
                        $this->parent->calls[] = [$key, $seconds, $value];
                        $this->parent->data[$key] = $value;
                        return true;
                    }

                    public function get($key)
                    {
                        return $this->parent->data[$key] ?? null;
                    }
                };
            }
        };

        $this->app->instance(RedisFactory::class, $redis);

        Http::fakeSequence()->push('redis-content', 200)->push('new-content', 200);

        $fetcher = $this->fetcher();

        $this->assertSame('redis-content', $fetcher->callFetch('https://example.com/test'));
        $this->assertSame(123, $redis->calls[0][1]);

        // Second call should hit cache and not trigger HTTP request
        $this->assertSame('redis-content', $fetcher->callFetch('https://example.com/test'));
        Http::assertSentCount(1);
    }
}
