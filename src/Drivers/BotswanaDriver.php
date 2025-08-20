<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Log;

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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::BWP;

    /**
     * @var string
     */
    private string $csv;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        try {
            $respond = $this->fetch(static::URI, $this->queryString());
            if ($respond) {
                $this->csv = $respond;
                $this->parseResponse();
            }
        } catch (\Throwable $e) {
            Log::debug('Can\'t connect to serwer', $e->getTrace());
        }

        return $this;
    }

    /**
     * @return array
     */
    private function queryString(): array
    {
        return [
            'field_exchange_date_value' => [
                'min' => $this->date->format('m/d/Y'),
                'max' => $this->date->format('m/d/Y'),
            ],
            'page' => '',
            '_format' => 'csv',
        ];
    }

    private function parseResponse()
    {
        $csv = $this->parseCsv($this->csv);

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
                return DateTimeImmutable::createFromFormat('Y-m-d', $item['date'])->getTimestamp() >=
                    $this->lastDate->getTimestamp();
            });
        }
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Bank of Botswana';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.bankofbotswana.bw';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.botswana.frequency');
    }
}
