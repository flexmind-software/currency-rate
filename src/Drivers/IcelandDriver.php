<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class IcelandDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.cb.is/statistics/official-exchange-rate/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'iceland';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_ISK;

    /**
     * @var array
     */
    private array $inputs;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $this->getParamArray();

        $respond = Http::asForm()->post(static::URI, $this->queryString($date));
        if ($respond->ok()) {
            $this->html = $respond->body();
            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    private function getParamArray()
    {
        $respond = Http::get(static::URI);
        if ($respond->ok()) {
            $response = $respond->body();

            $xpath = $this->htmlParse($response);
            $hiddenInput = $xpath->query('//*[@id="aspnetForm"]/*/input');

            $this->inputs = [];
            /**
             * @var \DOMElement $hidden
             */
            foreach ($hiddenInput as $hidden) {
                $this->inputs[$hidden->getAttribute('name')] = $hidden->getAttribute('value');
            }
        }
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function queryString(DateTime $date): array
    {
        $this->inputs['ctl00$ctl00$Content$Content$ctl04$ddlDays'] = $date->format('j');
        $this->inputs['ctl00$ctl00$Content$Content$ctl04$ddlMonths'] = $date->format('n');
        $this->inputs['ctl00$ctl00$Content$Content$ctl04$ddlYears'] = $date->format('Y');
        $this->inputs['ctl00$ctl00$Content$Content$ctl04$btnGetGengi'] = 'Search';

        return $this->inputs;
    }

    private function parseResponse()
    {
        $xpath = $this->htmlParse();

        $dateText = $xpath->query('//span[@id="ctl00_ctl00_Content_Content_ctl04_lblDisplayDate"]');
        $date = last(explode(': ', $dateText->item(0)->nodeValue));

        $table = $xpath->query('//table[@class="Gengistafla"]');
        if ($table = $table->item(0)) {
            $tableRow = [];
            foreach ($table->childNodes as $row => $tr) {
                foreach ($tr->childNodes as $td) {
                    $tableRow[$row][] = $this->clearRow($td->nodeValue);
                }
            }

            $this->data = [];
            foreach ($tableRow as $i => $item) {
                if ($item[1] === 'Currency') {
                    continue;
                }

                $this->data[] = [
                    'no' => null,
                    'code' => $item[1],
                    'date' => $date,
                    'driver' => static::DRIVER_NAME,
                    'multiplier' => $this->stringToFloat(1),
                    'rate' => $this->stringToFloat($item[5]),
                ];
            }
        }
    }

    public function fullName(): string
    {
        return 'Se??labanki ??slands';
    }

    public function homeUrl(): string
    {
        return 'https://cb.is';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
