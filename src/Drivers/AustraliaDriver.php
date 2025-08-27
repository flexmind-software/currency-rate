<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class AustraliaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.rba.gov.au/rss/rss-cb-exchange-rates.xml';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'australia';
    /**
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::AUD;

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

    private function parseResponse(): void
    {
        $xml = $this->parseXml($this->xml);

        $currencyList = [];
        foreach ($xml->item as $item) {
            $spaced = explode(' ', (string)$item->title);

            $target = $spaced[2];
            switch ($target) {
                case 'SDR': // Special Drawing Rights
                    $target = 'XDR';

                    break;
                case 'TWI_4pm':
                    continue 2;
            }

            $currencyList[] = [
                'no' => null,
                'code' => $target,
                'date' => $spaced[6],
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat($spaced[4]),
                'rate' => $this->stringToFloat($spaced[1]),
            ];
        }

        $this->data = $currencyList;
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Reserve Bank of Australia';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.rba.gov.au/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.australia.frequency');
    }
}
