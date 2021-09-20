<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class SwitzerlandDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.snb.ch/selector/en/mmr/exfeed/rss';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'switzerland';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_CHF;

    /**
     * @var string
     */
    private string $xml;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $respond = Http::get(static::URI);
        if ($respond->ok()) {
            $this->xml = $respond->body();
            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    private function parseResponse()
    {
        $simpleXMLElement = simplexml_load_string($this->xml, "SimpleXMLElement", LIBXML_NOCDATA, "", true);

        foreach ($simpleXMLElement->channel->item as $line) {
            preg_match(
                "/(CH:\s)(.*)\s(.{3})\s\=\s(\d+)\s(.{3})\s([0-9]{4}-[0-9]{2}-[0-9]{2})(.*)/im",
                (string)$line->title,
                $match
            );

            if ($match) {
                $this->data[] = [
                    'no' => null,
                    'code' => $match[5],
                    'date' => DateTime::createFromFormat('Y-m-d', trim($match[6])),
                    'driver' => static::DRIVER_NAME,
                    'multiplier' => $this->stringToFloat(trim($match[4])),
                    'rate' => $this->stringToFloat(trim($match[2])),
                ];
            }
        }
    }
}
