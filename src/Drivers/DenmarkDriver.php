<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

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
    public string $currency = Currency::CUR_DKK;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $response = Http::get($this->sourceUrl());
        if ($response->ok()) {
            $xml = $response->body();

            $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
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
