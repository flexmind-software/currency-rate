<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

/**
 *
 */
class BelarusDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     *
     * https://www.nbrb.by/engl/statistics/rates/ratesdaily.asp
     */
    public const URI = 'https://www.nbrb.by/engl/statistics/rates/ratesdaily.asp';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'belarus';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_BYN;
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
        $this->date = $date;

        $response = Http::asForm()
            ->post(static::URI, $this->getFormParams($date));

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
    private function getFormParams(DateTime $date): array
    {
        // query send over POST method
        return [
//            'Date' => $date->format('Y-m-d'),
            'Date' => $date->format('d/m/Y'),
            'Type' => 'Day',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
    }

    /**
     *
     */
    private function parseResponse()
    {
        $this->data = [];

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($this->html);
        $xpath = new DOMXpath($dom);

        $tableRows = $xpath->query('//table/tbody/tr');
        foreach ($tableRows as $row => $tr) {
            foreach ($tr->childNodes as $td) {
                $this->data[$row][] = $this->clearRow($td->nodeValue);
            }
            $this->data[$row] = array_values(array_filter($this->data[$row]));
        }

        $this->data = array_map(function ($item) {
            [$multiplier, $code] = explode(' ', $item[1]);

            return [
                'no' => null,
                'code' => $code,
                'date' => $this->date->format('Y-m-d'),
                'driver' => static::DRIVER_NAME,
                'multiplier' => floatval($multiplier),
                'rate' => floatval($item[2]),
            ];
        }, $this->data);
    }

    /**
     * @param DateTime $date
     *
     * @return string
     */
    private function sourceUrl(DateTime $date): string
    {
        return sprintf('%s', static::URI);
    }
}
