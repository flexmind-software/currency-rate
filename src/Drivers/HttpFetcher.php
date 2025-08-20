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
