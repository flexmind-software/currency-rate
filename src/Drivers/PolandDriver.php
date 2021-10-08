<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use Exception;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class PolandDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.nbp.pl/kursy/xml/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'poland';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_PLN;

    /**
     * @return self
     * @throws Exception
     */
    public function grabExchangeRates(): self
    {
        $response = Http::get(static::URI . 'dir.txt');
        if ($response->ok()) {
            $exchangeRateList = $response->body();

            $timestamp = $this->date->getTimestamp();
            if (intval(date('Hi', $timestamp)) < 1215) {
                $timestamp -= 86400;
            }

            $date = date('ymd', $timestamp);

            if (
                preg_match_all('/(a)([0-9]{3})z' . $date . '/', $exchangeRateList, $matches) &&
                ! blank($matches[0])
            ) {
                foreach ($matches[0] as $nbpNo) {
                    $response = Http::get(static::URI . $nbpNo . '.xml');
                    if ($response->ok()) {
                        $xml = $response->body();
                        $this->parseData($xml);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param string $xml
     *
     * @throws Exception
     */
    private function parseData(string $xml)
    {
        $currencies = new SimpleXMLElement($xml);
        $currencies = json_decode(json_encode($currencies), true);

        $param = [];
        $param['no'] = $currencies['numer_tabeli'];
        $param['driver'] = static::DRIVER_NAME;
        $param['date'] = $currencies['data_publikacji'];

        foreach ($currencies['pozycja'] as $position) {
            if (isset($position['kod_waluty']) &&
                isset($position['kurs_sredni']) &&
                isset($position['przelicznik'])
            ) {
                $param['code'] = strtoupper($position['kod_waluty']);
                $param['rate'] = $this->stringToFloat($position['kurs_sredni']);
                $param['multiplier'] = $this->stringToFloat($position['przelicznik']);
                $this->data[] = $param;
            }
        }
    }

    public function fullName(): string
    {
        return 'Narodowy Bank Polski';
    }

    public function homeUrl(): string
    {
        return 'https://www.nbp.pl/';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.poland.frequency');
    }
}
