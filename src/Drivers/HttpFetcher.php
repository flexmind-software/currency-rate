<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Psr\Http\Client\ClientInterface;
use SimpleXMLElement;
use Throwable;

trait HttpFetcher
{
    protected ?ClientInterface $httpClient = null;

    public function __construct(?ClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient;
    }

    protected function fetch(string $url, array $query = []): ?string
    {
        // --- CACHE KEY (kolejność parametrów nie ma znaczenia)
        ksort($query);
        $key = 'currency-rate:' . $url . (empty($query) ? '' : '?' . http_build_query($query));

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        // --- RETRY CONFIG
        $retry  = (array) config('currency-rate.retry', []);
        $tries  = max(1, (int) ($retry['count'] ?? 1));
        $sleep  = max(0, (int) ($retry['sleep'] ?? 1000)); // ms
        $factor = max(1, (int) ($retry['factor'] ?? 2));

        $attempt = 0;
        $exception = null;

        while ($attempt < $tries) {
            $attempt++;

            try {
                $body = null;

                if ($this->httpClient) {
                    // PSR-18 ścieżka
                    $uri = $url . (empty($query) ? '' : '?' . http_build_query($query));
                    $request = new Request('GET', $uri);
                    $response = $this->httpClient->sendRequest($request);

                    if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                        $body = (string) $response->getBody();
                    }
                } else {
                    // Laravel HTTP ścieżka
                    $response = Http::get($url, $query);
                    if ($response->ok()) {
                        $body = $response->body();
                    }
                }

                if ($body !== null) {
                    Cache::put($key, $body, config('currency-rate.cache-ttl'));
                    return $body;
                }

                // brak sukcesu → rzuć wyjątek żeby wejść w retry
                throw new \RuntimeException('Request failed with non-2xx status');
            } catch (Throwable $e) {
                $exception = $e;

                if ($attempt >= $tries) {
                    break; // koniec prób
                }

                // exponential backoff w ms -> usleep w µs
                $delayMs = (int) round($sleep * ($factor ** ($attempt - 1)));
                if ($delayMs > 0) {
                    usleep($delayMs * 1000);
                }
            }
        }

        // po wszystkich próbach zwracamy null (bez rzucania dalej)
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
