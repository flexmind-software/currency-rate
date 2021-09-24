<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class BotswanaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.bankofbotswana.bw/exchange-rates-export';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'botswana';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_BWP;

    /**
     * @var string
     */
    private string $csv;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $respond = Http::get(static::URI, $this->queryString($date));
        if ($respond->ok()) {
            $this->csv = $respond->body();

            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function queryString(DateTime $date): array
    {
        return [
            'field_exchange_date_value' => [
                'min' => $date->format('m/d/Y'),
                'max' => $date->format('m/d/Y')
                ],
            'page' => '',
            '_format' => 'csv'
        ];
    }

    private function parseResponse()
    {
        $csv = explode("\n", $this->csv);
        $csv = array_map('str_getcsv', $csv);

        $headers = head($csv);

        $this->data = [];
        foreach ($csv as $i => $row) {
            if ($i === 0) {
                continue;
            }

            foreach ($row as $k => $line) {
                if ($k === 0) {
                    continue;
                }
                $this->data[] = [
                    'no' => null,
                    'code' => $headers[$k],
                    'date' => date('Y-m-d', strtotime($row[0])),
                    'driver' => static::DRIVER_NAME,
                    'multiplier' => $this->stringToFloat(1),
                    'rate' => $this->stringToFloat($line),
                ];
            }
        }

        if ($this->lastDate) {
            $this->data = array_filter($this->data, function ($item) {
                return DateTime::createFromFormat('Y-m-d', $item['date'])->getTimestamp() >=
                    $this->lastDate->getTimestamp();
            });
        }
    }

    public function fullName(): string
    {
        return 'Bank of Botswana';
    }

    public function homeUrl(): string
    {
        return 'https://www.bankofbotswana.bw';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
