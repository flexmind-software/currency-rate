<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use Carbon\Carbon;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;

class NbpDriver implements CurrencyInterface
{
    private array $config;

    public function __construct()
    {
        $this->config = config('currency-rate.drivers.nbp');
    }

    public function downloadRates(Carbon $date)
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

        $listOfCurses = file_get_contents('http://www.nbp.pl/kursy/xml/dir.txt');
        if (preg_match_all('/([abch]([0-9]{3})z' . $date . '/', $listOfCurses, $matches)) {
            if (!empty($matches[0][0])) {
                $nbpNo = $matches[0][0];

                if (empty($nbpExchangeRate)) {
                    $xml = file_get_contents('http://www.nbp.pl/kursy/xml/' . $nbpNo . '.xml');
                    if (!empty($xml)) {
                        $currencies = new \SimpleXMLElement($xml);



                    } else {
                        \Log::error('No XML: ' . $nbpNo . '.xml');
                    }
                } else {
                    \Log::error('NBP currency: ' . $nbpNo . ' is already in the db');
                }
            }
        } else {
            \Log::error('No NBP currency for: ' . date('Y-m-d', $timestamp));
        }
    }
}
