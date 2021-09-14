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

        $listOfCurses = file_get_contents($this->config['url'] . 'dir.txt');

        if (preg_match_all('/([abch])([0-9]{3})z' . $date . '/', $listOfCurses, $matches)) {
            if (! blank($matches[0])) {
                foreach ($matches[0] as $match) {
                    $nbpNo = $match;
                    $xml = file_get_contents($this->config['url'] . $nbpNo . '.xml');
                    if (! empty($xml)) {
                        $currencies = new \SimpleXMLElement($xml);
                        $currencies = json_decode(json_encode($currencies), true);
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
