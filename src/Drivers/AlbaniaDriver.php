<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DateTimeImmutable;
use DOMNodeList;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use FlexMindSoftware\CurrencyRate\Support\Logger;
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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::ALL;
    /**
     * @var DOMNodeList|false
     */
    private $tables;

    /**
     * @var DOMXPath
     */
    private DOMXPath $xpath;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        do {
            $respond = Http::asForm()->post(static::URI, $this->formParam($this->date));
            if ($respond->ok()) {
                $this->html = $respond->body();
                $this->xpath = $this->htmlParse();
                $this->tables = $this->xpath->query('//table');
            }
        } while ($this->tables && $this->tables->count() == 0);

        if ($this->tables) {
            $this->parseResponse();
        }

        return $this;
    }

    /**
     * @param DateTimeImmutable $date
     *
     * @return array
     */
    private function formParam(DateTimeImmutable $date): array
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
        $date = DateTimeImmutable::createFromFormat('d.m.Y', $matches[2])->format('Y-m-d');

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

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'Bankës së Shqipërisë';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.bankofalbania.org/home/';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.albania.frequency');
    }
}
