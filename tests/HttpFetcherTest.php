<?php

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Drivers\HttpFetcher;
use Illuminate\Support\Facades\Http;

class HttpFetcherTest extends TestCase
{
    /** @test */
    public function fetch_returns_body_on_success()
    {
        Http::fake([
            'example.com/*' => Http::response('content', 200),
        ]);

        $fetcher = new class {
            use HttpFetcher;
        };

        $this->assertEquals('content', $fetcher->fetch('https://example.com/test'));
    }

    /** @test */
    public function parse_xml_returns_simplexml_element()
    {
        $fetcher = new class {
            use HttpFetcher;
        };

        $xml = '<root><item>value</item></root>';
        $parsed = $fetcher->parseXml($xml);

        $this->assertEquals('value', (string)$parsed->item);
    }

    /** @test */
    public function parse_csv_splits_rows()
    {
        $fetcher = new class {
            use HttpFetcher;
        };

        $csv = "a;b\n1;2";
        $parsed = $fetcher->parseCsv($csv, ';');

        $this->assertEquals(['a', 'b'], $parsed[0]);
        $this->assertEquals(['1', '2'], $parsed[1]);
    }
}

