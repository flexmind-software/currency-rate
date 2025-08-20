<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Drivers\HttpFetcher;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpFetcherTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /**
     * Helper: zwraca klasę-anonimową z traitem.
     * Jeśli przekażesz $client, trafi do traitowego __construct.
     */
    private function fetcher(?ClientInterface $client = null)
    {
        return new class ($client) {
            use HttpFetcher;

            public function callFetch($url, $query = [])
            {
                return $this->fetch($url, $query);
            }

            public function callParseXml($xml)
            {
                return $this->parseXml($xml);
            }

            public function callParseCsv($csv, $delimiter = ';')
            {
                return $this->parseCsv($csv, $delimiter);
            }
        };
    }

    /** @test */
    public function fetch_returns_body_on_success()
    {
        Http::fake([
            'example.com/*' => Http::response('content', 200),
        ]);

        $fetcher = $this->fetcher();
        $this->assertEquals('content', $fetcher->callFetch('https://example.com/test'));
    }

    /** @test */
    public function fetch_retries_on_failure_and_returns_body()
    {
        Http::fake([
            'example.com/*' => Http::sequence()
                ->push('error', 500)
                ->push('content', 200),
        ]);

        config()->set('currency-rate.retry.count', 3);
        config()->set('currency-rate.retry.sleep', 0);
        config()->set('currency-rate.retry.factor', 1);

        $fetcher = $this->fetcher();

        $this->assertEquals('content', $fetcher->callFetch('https://example.com/test'));
        Http::assertSentCount(2);
    }

    /** @test */
    public function fetch_returns_null_after_all_retries_fail()
    {
        Http::fake([
            'example.com/*' => Http::sequence()
                ->push('error', 500)
                ->push('error', 500),
        ]);

        config()->set('currency-rate.retry.count', 2);
        config()->set('currency-rate.retry.sleep', 0);
        config()->set('currency-rate.retry.factor', 1);

        $fetcher = $this->fetcher();

        $this->assertNull($fetcher->callFetch('https://example.com/test'));
        Http::assertSentCount(2);
    }

    /** @test */
    public function fetch_uses_injected_client_when_provided()
    {
        $client = new class () implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                // upewnijmy się, że PSR-18 ścieżka jest używana
                return new Response(200, [], 'psr-18');
            }
        };

        $fetcher = $this->fetcher($client);

        $this->assertEquals('psr-18', $fetcher->callFetch('https://example.com/test'));
    }

    /** @test */
    public function fetch_caches_successful_response()
    {
        Http::fakeSequence()
            ->push('content', 200)
            ->push('new-content', 200);

        $fetcher = $this->fetcher();

        // pierwszy call zapisuje do cache
        $this->assertEquals('content', $fetcher->callFetch('https://example.com/test', ['a' => 1]));
        // drugi call z tym samym query powinien wyciągnąć z cache
        $this->assertEquals('content', $fetcher->callFetch('https://example.com/test', ['a' => 1]));

        // tylko jedno realne żądanie HTTP
        Http::assertSentCount(1);
    }

    /** @test */
    public function parse_xml_returns_simplexml_element()
    {
        $fetcher = $this->fetcher();

        $xml = '<root><item>value</item></root>';
        $parsed = $fetcher->callParseXml($xml);

        $this->assertEquals('value', (string) $parsed->item);
    }

    /** @test */
    public function parse_csv_splits_rows()
    {
        $fetcher = $this->fetcher();

        $csv = "a;b\n1;2";
        $parsed = $fetcher->callParseCsv($csv, ';');

        $this->assertEquals(['a', 'b'], $parsed[0]);
        $this->assertEquals(['1', '2'], $parsed[1]);
    }

    /** @test */
    public function parse_csv_handles_quoted_delimiters_and_escapes()
    {
        $fetcher = $this->fetcher();

        $csv = <<<'CSV'
"a;b";"c""d"
"e;f";"g""h"
CSV;

        $parsed = $fetcher->callParseCsv($csv, ';');

        $this->assertEquals(['a;b', 'c"d'], $parsed[0]);
        $this->assertEquals(['e;f', 'g"h'], $parsed[1]);
    }
}
