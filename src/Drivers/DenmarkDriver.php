<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
     * @var string
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
            $json = json_decode(json_encode($xml), true);

            $this->parseDate($json);
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
     * @param array $jsonData
     */
    private function parseDate(array $jsonData)
    {
        foreach ($jsonData['Cube'] ?? [] as $children) {
            foreach ($children as $k => $child) {
                if (! empty($child['@data']['time'])) {
                    $this->data[$k]['time'] = $child['@data']['time'];

                    foreach ($child['Cube'] ?? [] as $node) {
                        if (! empty($node['@data'])) {
                            $this->data[$k]['rates'][$node['@data']['currency']] = $node['@data']['rate'];
                        }
                    }
                }
            }
        }
    }

    public function fullName(): string
    {
        return 'Danmarks Nationalbanks';
    }

    public function homeUrl(): string
    {
        return 'https://www.nationalbanken.dk';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.denmark.frequency');
    }
}
