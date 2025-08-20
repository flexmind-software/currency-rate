<?php
declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use Exception;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::PLN;

    /**
     * @return self
     * @throws Exception
     */
    public function grabExchangeRates(): self
    {
        $exchangeRateList = $this->fetch(static::URI . 'dir.txt');
        if ($exchangeRateList) {

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
                    $xml = $this->fetch(static::URI . $nbpNo . '.xml');
                    if ($xml) {
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
    private function parseData(string $xml): void
    {
        $currencies = $this->parseXml($xml);

        $param = [
            'no' => (string) $currencies->numer_tabeli,
            'driver' => static::DRIVER_NAME,
            'date' => (string) $currencies->data_publikacji,
        ];

        foreach ($currencies->pozycja as $position) {
            if (isset($position->kod_waluty, $position->kurs_sredni, $position->przelicznik)) {
                $param['code'] = strtoupper((string) $position->kod_waluty);
                $param['rate'] = $this->stringToFloat((string) $position->kurs_sredni);
                $param['multiplier'] = $this->stringToFloat((string) $position->przelicznik);
                $this->data[] = $param;
            }
        }
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Narodowy Bank Polski';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.nbp.pl/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.poland.frequency');
    }
}
