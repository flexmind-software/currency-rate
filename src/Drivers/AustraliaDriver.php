<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

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
     * @var string
     */
    public string $currency = Currency::CUR_AUD;

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
        $xml = simplexml_load_string($this->xml, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_decode(json_encode($xml), true);

        $currencyList = [];
        foreach ($xml->item as $item) {
            $spaced = explode(' ', (string) $item->title);

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

    public function fullName(): string
    {
        return 'Reserve Bank of Australia';
    }

    public function homeUrl(): string
    {
        return 'https://www.rba.gov.au/';
    }

    public function infoAboutFrequency(): string
    {
        return 'Weekdays around 4.00 PM Eastern Australian Time';
    }
}
