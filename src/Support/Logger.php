<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Support;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class Logger
{
    private static function channel(): LoggerInterface
    {
        $channel = config('currency-rate.log_channel');

        if ($channel === null) {
            $channel = config('logging.default');
        }

        return Log::channel($channel);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return self::channel()->{$name}(...$arguments);
    }
}

