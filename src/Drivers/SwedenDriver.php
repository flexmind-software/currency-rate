<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

/**
 *
 */
class SwedenDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     *
     * eg. https://www.riksbank.se/en-gb/statistics/search-interest--exchange-rates/
     */
    public const URI = 'https://www.riksbank.se/en-gb/statistics/search-interest--exchange-rates/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'sweden';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_DKK;
    /**
     * @var string
     */
    private string $response;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $response = Http::get(static::URI, $this->getQueryString($date));
        if ($response->ok()) {
            $this->response = $response->body();

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
            'to' => $date->format('d/m/y'),
            'from' => $date->sub(\DateInterval::createFromDateString('1 day'))->format('d/m/y'),
            'c' => 'cAverage',
            'f' => 'Day',
            's' => 'Dot',
            'export' => 'csv',
            'g130-SEKATSPMI' => 'on',
            'g130-SEKAUDPMI' => 'on',
            'g130-SEKBEFPMI' => 'on',
            'g130-SEKBRLPMI' => 'on',
            'g130-SEKCADPMI' => 'on',
            'g130-SEKCHFPMI' => 'on',
            'g130-SEKCNYPMI' => 'on',
            'g130-SEKCYPPMI' => 'on',
            'g130-SEKCZKPMI' => 'on',
            'g130-SEKDEMPMI' => 'on',
            'g130-SEKDKKPMI' => 'on',
            'g130-SEKEEKPMI' => 'on',
            'g130-SEKESPPMI' => 'on',
            'g130-SEKEURPMI' => 'on',
            'g130-SEKFIMPMI' => 'on',
            'g130-SEKFRFPMI' => 'on',
            'g130-SEKGBPPMI' => 'on',
            'g130-SEKGRDPMI' => 'on',
            'g130-SEKHKDPMI' => 'on',
            'g130-SEKHUFPMI' => 'on',
            'g130-SEKIDRPMI' => 'on',
            'g130-SEKIEPPMI' => 'on',
            'g130-SEKINRPMI' => 'on',
            'g130-SEKISKPMI' => 'on',
            'g130-SEKITLPMI' => 'on',
            'g130-SEKJPYPMI' => 'on',
            'g130-SEKKRWPMI' => 'on',
            'g130-SEKKWDPMI' => 'on',
            'g130-SEKLTLPMI' => 'on',
            'g130-SEKLVLPMI' => 'on',
            'g130-SEKMADPMI' => 'on',
            'g130-SEKMXNPMI' => 'on',
            'g130-SEKMYRPMI' => 'on',
            'g130-SEKNLGPMI' => 'on',
            'g130-SEKNOKPMI' => 'on',
            'g130-SEKNZDPMI' => 'on',
            'g130-SEKPLNPMI' => 'on',
            'g130-SEKPTEPMI' => 'on',
            'g130-SEKRUBPMI' => 'on',
            'g130-SEKSARPMI' => 'on',
            'g130-SEKSGDPMI' => 'on',
            'g130-SEKSITPMI' => 'on',
            'g130-SEKSKKPMI' => 'on',
            'g130-SEKTHBPMI' => 'on',
            'g130-SEKTRLPMI' => 'on',
            'g130-SEKTRYPMI' => 'on',
            'g130-SEKUSDPMI' => 'on',
            'g130-SEKZARPMI' => 'on',
        ];
    }

    /**
     *
     */
    private function parseResponse()
    {
        $rows = explode("\r\n", $this->response);
        $rows = array_map(function ($line) {
            return explode(';', $line);
        }, $rows);

        $rows = array_filter($rows, function ($line) {
            return count($line) === 4;
        });

        foreach ($rows as $k => $row) {
            if ($k === 0) {
                continue;
            }
            [$multiplier, $code] = explode(' ', $row[2]);
            [$day, $month, $year] = explode('/', $row[0]); //16/09/2021

            $this->data[] = [
                'date' => sprintf('%s-%s-%s', $year, $month, $day),
                'driver' => static::DRIVER_NAME,
                'code' => $code,
                'rate' => (float)($row[3] == 'n/a' ? 0 : $row[3]),
                'multiplier' => (int)$multiplier
            ];
        }
    }
}
