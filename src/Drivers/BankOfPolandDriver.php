<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use Exception;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use SimpleXMLElement;

class BankOfPolandDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    public const URI = 'https://www.nbp.pl/kursy/xml/';

    /**
     * @var string
     */
    public string $currency = Currency::CUR_PLN;

    /**
     * @var string
     */
    private string $driverAlias = 'bank-of-poland';

    /**
     * @param DateTime $date
     *
     * @return mixed|void
     * @throws Exception
     */
    public function downloadRates(DateTime $date)
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

        $listOfCurses = file_get_contents(static::URI . 'dir.txt');

        if (preg_match_all('/(a)([0-9]{3})z' . $date . '/', $listOfCurses, $matches)) {
            if (!blank($matches[0])) {
                foreach ($matches[0] as $match) {
                    $nbpNo = $match;
                    $xml = file_get_contents(static::URI . $nbpNo . '.xml');
                    if (!empty($xml)) {
                        $currencies = new SimpleXMLElement($xml);
                        $currencies = json_decode(json_encode($currencies), true);

                        $param = [];
                        $param['no'] = $currencies['numer_tabeli'];
                        $param['driver'] = 'bank-of-poland';
                        $param['date'] = $currencies['data_publikacji'];

                        $toSave = [];
                        foreach ($currencies['pozycja'] as $position) {
                            if (isset($position['kod_waluty']) &&
                                isset($position['kurs_sredni']) &&
                                isset($position['przelicznik'])
                            ) {
                                $param['code'] = strtoupper($position['kod_waluty']);
                                if (!count($this->config['supported-currency']) ||
                                    in_array($param['code'], $this->config['supported-currency'])
                                ) {
                                    $param['rate'] = floatval(
                                        str_replace(',', '.', $position['kurs_sredni'])
                                    );
                                    $param['multiplier'] = floatval(
                                        str_replace(',', '.', $position['przelicznik'])
                                    );
                                    $toSave[] = $param;
                                }
                            }
                        }

                        CurrencyRate::upsert($toSave, ['no', 'driver', 'code', 'date'], ['rate', 'multiplier']);
                    }
                }
            }
        }
    }
}
