<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Psr\Http\Client\ClientInterface;
use SimpleXMLElement;

trait HttpFetcher
{
    protected ?ClientInterface $httpClient = null;

    public function __construct(?ClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient;
    }

    protected function fetch(string $url, array $query = []): ?string
    {
        ksort($query);
        $key = 'currency-rate:' . $url . (empty($query) ? '' : '?' . http_build_query($query));

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $body = null;

        if ($this->httpClient) {
            // Ścieżka PSR-18
            $uri = $url . (empty($query) ? '' : '?' . http_build_query($query));
            $request = new Request('GET', $uri);
            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $body = (string) $response->getBody();
            }
        } else {
            // Domyślnie Laravel HTTP client
            $response = Http::get($url, $query);
            if ($response->ok()) {
                $body = $response->body();
            }
        }

        if ($body !== null) {
            Cache::put($key, $body, config('currency-rate.cache-ttl'));
            return $body;
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
