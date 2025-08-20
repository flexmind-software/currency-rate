<?php
declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::CHF;

    /**
     * @var string
     */
    private string $xml;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $respond = $this->fetch(static::URI);
        if ($respond) {
            $this->xml = $respond;
            $this->parseResponse();
        }

        return $this;
    }

    private function parseResponse()
    {
        $simpleXMLElement = $this->parseXml($this->xml, LIBXML_NOCDATA, '', true);

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
                    'date' => DateTimeImmutable::createFromFormat('Y-m-d', trim($match[6])),
                    'driver' => static::DRIVER_NAME,
                    'multiplier' => $this->stringToFloat(trim($match[4])),
                    'rate' => $this->stringToFloat(trim($match[2])),
                ];
            }
        }
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Swiss National Bank';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.snb.ch/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.switzerland.frequency');
    }
}
