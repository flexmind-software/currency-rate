<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use GuzzleHttp\Psr7\Request;
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
        if ($this->httpClient) {
            $uri = $url . (empty($query) ? '' : '?' . http_build_query($query));
            $request = new Request('GET', $uri);
            $response = $this->httpClient->sendRequest($request);

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300
                ? (string) $response->getBody()
                : null;
        }

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
