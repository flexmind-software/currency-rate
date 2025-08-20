<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use Exception;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use SimpleXMLElement;

class EuropeanCentralBankDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'european-central-bank';
    /**
     * @var string
     */
    public CurrencyCode $currency = CurrencyCode::EUR;

    /**
     * @return self
     * @throws Exception
     */
    public function grabExchangeRates(): self
    {
        $string = $this->fetch(static::URI);
        if ($string) {
            $xml = $this->parseXml($string);

            $this->parseDate($xml->Cube->Cube);
            $this->findByDate('date');
        }

        return $this;
    }

    /**
     * @param SimpleXMLElement $jsonData
     */
    private function parseDate(SimpleXMLElement $jsonData)
    {
        $jsonData = json_decode(json_encode($jsonData), true);
        foreach ($jsonData['Cube'] ?? [] as $children) {
            foreach ($children as $node) {
                $this->data[$node['currency']]['date'] = $jsonData['@attributes']['time'];
                $this->data[$node['currency']]['rate'] = floatval($node['rate']);
                $this->data[$node['currency']]['multiplier'] = 1;
                $this->data[$node['currency']]['no'] = null;
                $this->data[$node['currency']]['driver'] = static::DRIVER_NAME;
                $this->data[$node['currency']]['code'] = $node['currency'];
            }
        }
    }

    public function fullName(): string
    {
        return 'European Central Bank';
    }

    public function homeUrl(): string
    {
        return 'https://www.ecb.europa.eu/';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.european-central-bank.frequency');
    }
}
