<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class NorwayDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://data.norges-bank.no/api/data/EXR/B..NOK.SP';
    /**
     * @var string
     */
    public const DRIVER_NAME = 'norway';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_NOK;
    /**
     * @var array
     */
    protected array $json;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $response = Http::get(static::URI, $this->getQueryString());
        if ($response->ok()) {
            $this->json = $response->json();
            $this->parseBody();
        }
        return $this;
    }

    /**
     * @return array
     */
    private function getQueryString(): array
    {
        return [
            'endPeriod' => $this->date->format('Y-m-d'),
            'startPeriod' => $this->date
                ->sub(DateInterval::createFromDateString('1 day'))
                ->format('Y-m-d'),
            'format' => 'sdmx-json',
            'locale' => 'en',
        ];
    }

    private function parseBody()
    {
        $no = $this->json['meta']['id'];

        $timePeriod = $this->json['data']['structure']['dimensions']['observation'];
        $currencies = $this->json['data']['structure']['dimensions']['series'][1]['values'];
        $currencies = array_column($currencies, 'id');

        $dataSet = $this->json['data']['dataSets'] ?? [];

        foreach ($dataSet as $serieId => $line) {
            foreach ($line['series'] as $id => $item) {
                $id = explode(':', $id);
                [$decimals, $calculated, $unitMulti, $collection] = $item['attributes'];
                $this->data[] = [
                    'date' => $timePeriod[$serieId]['values'][0]["name"],
//                    'no' => $no,
                    'driver' => static::DRIVER_NAME,
                    'code' => strtoupper($currencies[$id[1]]),
                    'rate' => (float)head(head($item['observations'])),
                    'multiplier' => pow(100, $unitMulti),
                ];
            }
        }
    }

    public function fullName(): string
    {
        return 'Norges Bank';
    }

    public function homeUrl(): string
    {
        return 'https://www.norges-bank.no/';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
