<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use Exception;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use FlexMindSoftware\CurrencyRate\Support\Logger;
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
     * @var CurrencyCode
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
     * Traverse SimpleXMLElement for date and currency data.
     */
    private function parseDate(SimpleXMLElement $cubes): void
    {
        foreach ($cubes as $cube) {
            $date = (string) $cube['time'];

            foreach ($cube->Cube as $node) {
                $currency = (string) $node['currency'];

                $this->data[$currency]['date'] = $date;
                $this->data[$currency]['rate'] = (float) $node['rate'];
                $this->data[$currency]['multiplier'] = 1;
                $this->data[$currency]['no'] = null;
                $this->data[$currency]['driver'] = static::DRIVER_NAME;
                $this->data[$currency]['code'] = $currency;
            }
        }
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'European Central Bank';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.ecb.europa.eu/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.european-central-bank.frequency');
    }
}
