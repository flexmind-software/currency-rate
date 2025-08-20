<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

trait HttpFetcher
{
    protected function fetch(string $url, array $query = []): ?string
    {
        ksort($query);
        $key = 'currency-rate:' . $url . '?' . http_build_query($query);

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = Http::get($url, $query);

        if ($response->ok()) {
            Cache::put($key, $response->body(), config('currency-rate.cache-ttl'));

            return $response->body();
        }

        return null;
    }

    protected function parseXml(
        string $xml,
        int $options = LIBXML_NOCDATA,
        string $ns = '',
        bool $isPrefix = false
    ): SimpleXMLElement {
        return simplexml_load_string($xml, 'SimpleXMLElement', $options, $ns, $isPrefix);
    }

    protected function parseCsv(string $csv, string $delimiter = ';'): array
    {
        $file = new \SplFileObject('php://temp', 'r+');
        $file->fwrite($csv);
        $file->rewind();
        $file->setCsvControl($delimiter);
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);

        $rows = [];
        foreach ($file as $row) {
            if ($row === [null] || $row === false) {
                continue;
            }
            $rows[] = $row;
        }

        return $rows;
    }
}
