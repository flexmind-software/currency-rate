<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use Exception;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class BankOfPolandDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.nbp.pl/kursy/xml/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'bank-of-poland';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_PLN;

    /**
     * @param DateTime $date
     *
     * @return void
     * @throws Exception
     */
    public function downloadRates(DateTime $date)
    {
        $this->retrieveData($date);
        $this->saveInDatabase();
    }

    /**
     * @param DateTime $date
     *
     * @throws Exception
     */
    private function retrieveData(DateTime $date)
    {
        $response = Http::get(static::URI . 'dir.txt');
        if ($response->ok()) {
            $listOfCurses = $response->body();

            $timestamp = $date->getTimestamp();

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

            if (preg_match_all('/(a)([0-9]{3})z' . $date . '/', $listOfCurses, $matches)) {
                if (! blank($matches[0])) {
                    foreach ($matches[0] as $nbpNo) {
                        $response = Http::get(static::URI . $nbpNo . '.xml');
                        if ($response->ok()) {
                            $xml = $response->body();
                            $this->parseData($xml);
                        }
                    }
                }
            }
        }
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
                if (! count($this->config['supported-currency']) ||
                    in_array($param['code'], $this->config['supported-currency'])
                ) {
                    $param['rate'] = floatval(
                        str_replace(',', '.', $position['kurs_sredni'])
                    );
                    $param['multiplier'] = floatval(
                        str_replace(',', '.', $position['przelicznik'])
                    );
                    $this->data[] = $param;
                }
            }
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
