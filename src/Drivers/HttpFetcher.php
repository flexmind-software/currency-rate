<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

trait HttpFetcher
{
    protected function fetch(string $url, array $query = []): ?string
    {
        $response = Http::get($url, $query);

        return $response->ok() ? $response->body() : null;
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
        $lines = preg_split('/\r\n|\n|\r/', trim($csv));

        return array_map(
            fn ($line) => str_getcsv($line, $delimiter),
            array_filter($lines)
        );
    }
}

