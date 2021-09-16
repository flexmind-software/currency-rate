<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

/**
 *
 */
class BankOfBosniaAndHerzegovinaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    // https://www.cbbh.ba/CurrencyExchange/GetJsonForPeriod?dateFrom=Sun,%2028%20Feb%202021%2023:00:00%20GMT&dateTo=Wed,%2030%20Jun%202021%2022:00:00%20GMT
    /**
     * @const string
     */
    public const URI = 'https://www.cbbh.ba/CurrencyExchange/GetJson';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'bank-of-bosnia-and-herzegovina';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_BAM;
    /**
     * @var array|mixed
     */
    private array $jsonData;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $response = Http::asJson()->get(static::URI, $this->getQueryString($date));
        if ($response->ok()) {
            $this->jsonData = $response->json();
            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function getQueryString(DateTime $date): array
    {
        return [
            'date' => $date->format("m/d/Y 00:00:00"),
        ];
    }

    private function parseResponse()
    {
        foreach ($this->jsonData['CurrencyExchangeItems'] ?? [] as $item) {
            $this->data[] = [
                'no' => $this->jsonData['Number'],
                'code' => $item['AlphaCode'],
                'date' => date('Y-m-d', strtotime($this->jsonData['Date'])),
                'driver' => static::DRIVER_NAME,
                'multiplier' => floatval($item['Units']),
                'rate' => floatval(str_replace(',', '.', $item['Middle'])),
            ];
        }
    }

    /**
     *
     */
    protected function saveInDatabase()
    {
        CurrencyRate::upsert($this->data, ['no', 'driver', 'code', 'date'], ['rate', 'multiplier']);
    }
}
