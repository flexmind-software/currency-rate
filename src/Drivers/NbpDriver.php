<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class NbpDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @var string
     */
    public string $currency = Currency::CUR_PLN;

    /**
     * @var string
     */
    private string $driverAlias = 'nbp';
    /**
     * @var array
     */
    private array $config;

    public function __construct()
    {
        $this->config = config('currency-rate');
    }

    /**
     * @param \DateTime $date
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function downloadRates(\DateTime $date)
    {
        $timestamp = $date->timestamp;

        /**
         * Tabela A kursów średnich walut obcych publikowana (aktualizowana) jest na stronie internetowej NBP w
         * dni robocze, pomiędzy godziną 11:45 a 12:15,
         * Tabela B kursów średnich walut obcych publikowana (aktualizowana) jest na stronie internetowej NBP w
         * środy, pomiędzy godziną 11:45 a 12:15,
         */
        // pobieramy z poprzedniego dnia
        if (intval(date('Hi', $timestamp)) < 1215) {
            $timestamp -= 86400;
        }

        $date = date('ymd', $timestamp);

        $config = $this->config['drivers'][$this->driverAlias];

        $listOfCurses = file_get_contents($config['url'] . 'dir.txt');

        if (preg_match_all('/([abch])([0-9]{3})z' . $date . '/', $listOfCurses, $matches)) {
            if (!blank($matches[0])) {
                foreach ($matches[0] as $match) {
                    $nbpNo = $match;
                    $xml = file_get_contents($config['url'] . $nbpNo . '.xml');
                    if (!empty($xml)) {
                        $currencies = new \SimpleXMLElement($xml);
                        $currencies = json_decode(json_encode($currencies), true);

                        $param = [];
                        $param['no'] = $currencies['numer_tabeli'];
                        $param['driver'] = 'nbp';
                        $param['date'] = $currencies['data_publikacji'];

                        $toSave = [];
                        foreach ($currencies['pozycja'] as $position) {
                            if (isset($position['kod_waluty']) &&
                                isset($position['kurs_sprzedazy']) &&
                                isset($position['przelicznik'])
                            ) {
                                $param['code'] = strtolower($position['kod_waluty']);
                                if (!count($this->config['supported-currency']) ||
                                    in_array(strtoupper($param['code']), $this->config['supported-currency'])
                                ) {
                                    $param['rate'] = floatval(
                                        str_replace(',', '.', $position['kurs_sprzedazy'])
                                    );
                                    $param['multiplier'] = floatval(
                                        str_replace(',', '.', $position['przelicznik'])
                                    );
                                    $toSave[] = $param;
                                }
                            }
                        }

                        CurrencyRate::upsert($toSave, ['no', 'driver', 'code', 'date'], ['rate', 'multiplier']);
                    } else {
                        \Log::error('No XML: ' . $nbpNo . '.xml');
                    }
                }
            }
        } else {
            \Log::error('No NBP currency for: ' . date('Y-m-d', $timestamp));
        }
    }
}
