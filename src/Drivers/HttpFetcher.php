<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Throwable;

trait HttpFetcher
{
    protected function fetch(string $url, array $query = []): ?string
    {
        $retry = config('currency-rate.retry');
        $count = (int) ($retry['count'] ?? 1);
        $sleep = (int) ($retry['sleep'] ?? 1000);
        $factor = (int) ($retry['factor'] ?? 2);

        try {
            return retry($count, function () use ($url, $query) {
                $response = Http::get($url, $query);

                if ($response->ok()) {
                    return $response->body();
                }

                throw new \Exception('Request failed');
            }, function (int $attempt) use ($sleep, $factor) {
                return (int) ($sleep * ($factor ** ($attempt - 1)));
            });
        } catch (Throwable $e) {
            return null;
        }
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
