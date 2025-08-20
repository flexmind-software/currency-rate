<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Drivers\HttpFetcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HttpFetcherTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function fetcher()
    {
        return new class () {
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
    public function fetch_caches_successful_response()
    {
        Http::fakeSequence()
            ->push('content', 200)
            ->push('new-content', 200);

        $fetcher = $this->fetcher();

        $this->assertEquals('content', $fetcher->callFetch('https://example.com/test', ['a' => 1]));
        $this->assertEquals('content', $fetcher->callFetch('https://example.com/test', ['a' => 1]));

        Http::assertSentCount(1);
    }

    /** @test */
    public function parse_xml_returns_simplexml_element()
    {
        $fetcher = $this->fetcher();

        $xml = '<root><item>value</item></root>';
        $parsed = $fetcher->callParseXml($xml);

        $this->assertEquals('value', (string)$parsed->item);
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
