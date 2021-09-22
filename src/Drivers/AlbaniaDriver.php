<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DateTime;
use DOMNodeList;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;

class AlbaniaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.bankofalbania.org/Markets/Official_exchange_rate/';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'albania';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_ALL;
    /**
     * @var DOMNodeList|false
     */
    private $tables;

    /**
     * @var DOMXPath
     */
    private DOMXPath $xpath;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        do {
            $respond = Http::asForm()->post(static::URI, $this->formParam($date));
            if ($respond->ok()) {
                $this->html = $respond->body();
                $this->xpath = $this->htmlParse();
                $this->tables = $this->xpath->query('//table');
            }
        } while ($this->tables && $this->tables->count() == 0);

        if ($this->tables) {
            $this->parseResponse();
            $this->saveInDatabase();
        }
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function formParam(DateTime $date): array
    {
        $dateToUni = now()->format('Ymd120148109196597389634909');

        $endDate = $date->format('d.m.Y');
        $startDate = $date->sub(DateInterval::createFromDateString('1 day'))->format('d.m.Y');

        return [
            'sourcePage' => '',
            'targetPage' => '',
            'sessionVars' => 'lang=Lng2&ln=2&contentId=11288&uni=' . $dateToUni . '&lng=en&recPages=multi&kk_rs_sr_CP=1&kkOther_rs_sr_CP=1&kk_rs_sr_bid_ask_CP=1&menyra_shfaqjes=T&crd=0,7,2,0,0,11288&',
            'phpVars' => 'event=kursi_kembimit.search_this(startDate=' . $startDate . ';endDate=' . $endDate . ';pubcat=99;menyra_shfaqjes=T)',
        ];
    }

    private function parseResponse()
    {
        $tables = $this->tables->item(0)->childNodes;

        $itemList = [];
        foreach ($tables as $i => $item) {
            if (is_numeric($i) && $code = $this->clearRow($item->nodeValue)) {
                $itemList[$i] = array_values(
                    array_filter(
                        explode("\t", $code)
                    )
                );
            }
        }

        preg_match('#(.*)Date\s([0-9]{2}\.[0-9]{2}\.[0-9]{4})(.+)#im', $this->html, $matches);
        $date = DateTime::createFromFormat('d.m.Y', $matches[2])->format('Y-m-d');

        $itemList = array_values($itemList);

        foreach ($itemList as $i => $value) {
            if ($i === 0) {
                continue;
            }

            $this->data[] = [
                'no' => null,
                'code' => $value[1],
                'date' => $date,
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat(1),
                'rate' => $this->stringToFloat(trim($value[2])),
            ];
        }
    }

    public function fullName(): string
    {
        return 'Bankës së Shqipërisë';
    }

    public function homeUrl(): string
    {
        return 'https://www.bankofalbania.org/home/';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
