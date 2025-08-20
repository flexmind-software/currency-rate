<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use SimpleXMLElement;

/**
 *
 */
class DenmarkDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.nationalbanken.dk/_vti_bin/DN/DataService.svc/CurrencyRatesHistoryXML';
    /**
     * @const string
     */
    public const QUERY_STRING = 'lang=en';
    /**
     * @var string
     */
    public const DRIVER_NAME = 'denmark';
    /**
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::DKK;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $response = $this->fetch($this->sourceUrl());
        if ($response) {
            $xml = $this->parseXml($response);

            $this->parseDate($xml);
            $this->findByDate('time');
        }

        return $this;
    }

    /**
     * @return string
     */
    private function sourceUrl(): string
    {
        return sprintf('%s?%s', static::URI, static::QUERY_STRING);
    }

    /**
     * Extract date and rates from SimpleXMLElement
     */
    private function parseDate(SimpleXMLElement $xml): void
    {
        foreach ($xml->Cube->Cube as $cube) {
            $time = (string) $cube['time'];
            $entry = ['time' => $time, 'rates' => []];

            foreach ($cube->Cube as $node) {
                $currency = (string) $node['currency'];
                $rate = (string) $node['rate'];
                if ($currency !== '' && $rate !== '') {
                    $entry['rates'][$currency] = $rate;
                }
            }

            $this->data[] = $entry;
        }
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Danmarks Nationalbanks';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.nationalbanken.dk';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.denmark.frequency');
    }
}
