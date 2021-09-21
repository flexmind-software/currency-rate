<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class HungaryDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    // https://www.mnb.hu/en/arfolyam-tablazat?deviza=rbCurrencyAll&devizaSelected=ZAR&datefrom=01%2F01%2F2021&datetill=15%2F09%2F2021&order=1
    /**
     * @const string
     */
    public const URI = 'https://www.mnb.hu/en/arfolyam-tablazat';
    /**
     * @var string
     */
    public const DRIVER_NAME = 'hungary';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_HUF;

    /**
     * @var string
     */
    private string $html;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $response = Http::get(static::URI, $this->queryString($date));
        if ($response->ok()) {
            $this->html = $response->body();
            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function queryString(DateTime $date): array
    {
        return [
            'deviza' => 'rbCurrencyAll',
            'devizaSelected' => 'ZAR',
            'datetill' => $date->format('d/m/Y'),
            'datefrom' => ($this->lastDate ?? $date)->format('01/01/Y'),
            'order' => 1,
        ];
    }

    private function parseResponse()
    {
        $this->data = [];

        $dom = new DOMDocument('1.0', 'UTF-8');

        preg_match_all('/<table.*?>(.*?)<\/table>/si', $this->html, $matches);

        $dom->loadHTML($matches[0][0]);
        $xpath = new DOMXpath($dom);

        $tableRows = $xpath->query("//table//thead//tr");

        $currencies = [];
        foreach ($tableRows[0]->childNodes as $c => $th) {
            if ($code = $this->clearRow($th->nodeValue)) {
                $currencies[] = [
                    'code' => $code,
                    'multiplier' => (int)$this->clearRow($tableRows[2]->childNodes[$c]->nodeValue),
                    'driver' => static::DRIVER_NAME,
                ];
            }
        }

        $this->data = [];
        $tableRows = $xpath->query("//table//tbody//tr");
        foreach ($tableRows as $r => $tr) {
            $row = [];
            foreach ($tr->childNodes as $c => $td) {
                $row[] = $this->clearRow($td->nodeValue);
            }

            $row = array_filter($row);
            $row = array_values($row);

            $date = array_shift($row);

            foreach ($row as $i => $item) {
                if ($item != '-') {
                    $line = $currencies[$i];
                    $line['date'] = date('Y-m-d', strtotime($date));
                    $line['rate'] = (float)$item;

                    $this->data[] = $line;
                }
            }
        }
    }

    public function fullName(): string
    {
        return '';
    }

    public function homeUrl(): string
    {
        return '';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
