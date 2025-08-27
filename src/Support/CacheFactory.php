<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Support;

use Illuminate\Cache\RedisStore;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Support\Facades\Cache;

class CacheFactory
{
    public static function make(?string $store = null): Repository
    {
        $store = $store ?? (string) config('currency-rate.cache_store', 'array');

        if ($store === 'redis') {
            /** @var RedisFactory $redis */
            $redis = app(RedisFactory::class);

            return new CacheRepository(
                new RedisStore($redis, (string) config('cache.prefix', ''))
            );
        }

        return Cache::store($store);
    }
}
