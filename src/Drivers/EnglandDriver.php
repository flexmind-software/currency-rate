<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DateTime;
use DOMNodeList;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class EnglandDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.bankofengland.co.uk/boeapps/database/Rates.asp';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'england';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_GBP;

    /**
     * @var string
     */
    private string $body;
    /**
     * @var DOMNodeList|false
     */
    private $tables = false;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        do {
            $respond = Http::get(static::URI, $this->queryString());
            if ($respond->ok()) {
                $xpath = $this->htmlParse($respond->body());
                $this->tables = $xpath->query('//table');
            }

            $date = $this->date->sub(DateInterval::createFromDateString('1 day'));
        } while ($this->tables && $this->tables->count() == 1);

        if ($this->tables) {
            $this->parseResponse();
        }

        return $this;
    }

    /**
     * @return array
     */
    private function queryString(): array
    {
        return [
            'TD' => $this->date->format('j'),
            'TM' => $this->date->format('M'),
            'TY' => $this->date->format('Y'),
            'into' => 'GBP',
            'rateview' => 'D',
        ];
    }

    private function parseResponse()
    {
        $tables = $this->tables->item(0)->childNodes;

        $itemList = [];
        foreach ($tables as $i => $item) {
            if (is_numeric($i) && $code = $this->clearRow($item->nodeValue)) {
                $itemList[$i] = array_values(
                    array_filter(
                        explode("\t", $code)
                    )
                );
            }
        }

        $date = trim(str_replace(' £1 ', '', head($itemList)[1]));
        $date = DateTime::createFromFormat('j M Y', $date)->format('Y-m-d');

        foreach ($itemList as $i => $value) {
            if ($value[0] == 'Currency') {
                continue;
            }
            $this->data[] = [
                'no' => null,
                'code' => $this->currencyMap(trim($value[0])),
                'date' => $date,
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat(1),
                'rate' => $this->stringToFloat(trim($value[1])),
            ];
        }
    }

    private function currencyMap(string $currencyName)
    {
        $map = [
            'Australian Dollar' => Currency::CUR_AUD,
            'Canadian Dollar' => Currency::CUR_CAD,
            'Chinese Yuan' => Currency::CUR_CNY,
            'Czech Koruna' => Currency::CUR_CZK,
            'Danish Krone' => Currency::CUR_DKK,
            'Euro' => Currency::CUR_EUR,
            'Hong Kong Dollar' => Currency::CUR_HKD,
            'Hungarian Forint' => Currency::CUR_HUF,
            'Indian Rupee' => Currency::CUR_INR,
            'Israeli Shekel' => Currency::CUR_ILS,
            'Japanese Yen' => Currency::CUR_JPY,
            'Malaysian ringgit' => Currency::CUR_MYR,
            'New Zealand Dollar' => Currency::CUR_NZD,
            'Norwegian Krone' => Currency::CUR_NOK,
            'Polish Zloty' => Currency::CUR_PLN,
            'Russian Ruble' => Currency::CUR_RUB,
            'Saudi Riyal' => Currency::CUR_SAR,
            'Singapore Dollar' => Currency::CUR_SGD,
            'South African Rand' => Currency::CUR_ZAR,
            'South Korean Won' => Currency::CUR_KRW,
            'Swedish Krona' => Currency::CUR_SEK,
            'Swiss Franc' => Currency::CUR_CHF,
            'Taiwan Dollar' => Currency::CUR_TWD,
            'Thai Baht' => Currency::CUR_THB,
            'Turkish Lira' => Currency::CUR_TRY,
            'US Dollar' => Currency::CUR_USD,
        ];

        return $map[$currencyName] ?? null;
    }

    public function fullName(): string
    {
        return 'Bank of England';
    }

    public function homeUrl(): string
    {
        return 'https://www.bankofengland.co.uk/';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
