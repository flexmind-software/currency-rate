<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DateTimeImmutable;
use DOMNodeList;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use FlexMindSoftware\CurrencyRate\Support\Logger;

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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::GBP;

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
            $respond = $this->fetch(static::URI, $this->queryString());
            if ($respond) {
                $xpath = $this->htmlParse($respond);
                $this->tables = $xpath->query('//table');
            }

            $this->date = $this->date->sub(DateInterval::createFromDateString('1 day'));
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
        $date = DateTimeImmutable::createFromFormat('j M Y', $date)->format('Y-m-d');

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
            'Australian Dollar' => CurrencyCode::AUD->value,
            'Canadian Dollar' => CurrencyCode::CAD->value,
            'Chinese Yuan' => CurrencyCode::CNY->value,
            'Czech Koruna' => CurrencyCode::CZK->value,
            'Danish Krone' => CurrencyCode::DKK->value,
            'Euro' => CurrencyCode::EUR->value,
            'Hong Kong Dollar' => CurrencyCode::HKD->value,
            'Hungarian Forint' => CurrencyCode::HUF->value,
            'Indian Rupee' => CurrencyCode::INR->value,
            'Israeli Shekel' => CurrencyCode::ILS->value,
            'Japanese Yen' => CurrencyCode::JPY->value,
            'Malaysian ringgit' => CurrencyCode::MYR->value,
            'New Zealand Dollar' => CurrencyCode::NZD->value,
            'Norwegian Krone' => CurrencyCode::NOK->value,
            'Polish Zloty' => CurrencyCode::PLN->value,
            'Russian Ruble' => CurrencyCode::RUB->value,
            'Saudi Riyal' => CurrencyCode::SAR->value,
            'Singapore Dollar' => CurrencyCode::SGD->value,
            'South African Rand' => CurrencyCode::ZAR->value,
            'South Korean Won' => CurrencyCode::KRW->value,
            'Swedish Krona' => CurrencyCode::SEK->value,
            'Swiss Franc' => CurrencyCode::CHF->value,
            'Taiwan Dollar' => CurrencyCode::TWD->value,
            'Thai Baht' => CurrencyCode::THB->value,
            'Turkish Lira' => CurrencyCode::TRY->value,
            'US Dollar' => CurrencyCode::USD->value,
        ];

        return $map[$currencyName] ?? null;
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Bank of England';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.bankofengland.co.uk/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.england.frequency');
    }
}
