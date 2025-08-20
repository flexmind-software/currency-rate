<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DOMElement;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;

class BceaoDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.bceao.int/en/cours/get_all_reference_by_date?dateJour=2021-09-10';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'bceao';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_XOF;
    /**
     * @var DOMXPath
     */
    private DOMXPath $xpath;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $exists = false;
        do {
            $respond = $this->fetch(static::URI, $this->queryString());
            if ($respond) {
                $this->html = '<head><meta charset="utf-8" /></head><body>' . $respond . '</body>';
                $this->xpath = $this->htmlParse();
                if (! ($exists = $this->xpath->query('//table')->count() > 0)) {
                    $this->date->sub(DateInterval::createFromDateString('1 day'));
                }
            }
        } while (! $exists);

        $this->parseResponse();

        return $this;
    }

    /**
     * @return array
     */
    private function queryString(): array
    {
        return [
            'dateJour' => $this->date->format('Y-m-d'),
        ];
    }

    private function parseResponse()
    {
        $xPath = $this->htmlParse();

        $data = $this->clearRow($xPath->query('//h2')->item(0)->nodeValue);
        preg_match('#(\d+)\s([a-z]+)\s([0-9]{4})#im', $data, $matches);
        $date = date('Y-m-d', strtotime($matches[0]));

        $rows = $xPath->query('//table/tbody/tr');

        $data = [];
        /** @var DOMElement $row */
        foreach ($rows as $i => $row) {
            if ($i === 0) {
                continue;
            }
            foreach ($row->childNodes as $c => $td) {
                if ($value = $this->clearRow($td->nodeValue)) {
                    $data[$i][] = $value;
                }
            }
        }

        $this->data = array_map(function ($item) use ($date) {
            return [
                'no' => null,
                'code' => $this->currencyMap($item[0]),
                'date' => $date,
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat(1),
                'rate' => $this->stringToFloat($item[1]),
            ];
        }, $data);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function currencyMap(string $name): string
    {
        $currencyList = [
            'Couronne danoise' => Currency::CUR_DKK,
            'Couronne suédoise' => Currency::CUR_SEK,
            'Couronne norvégienne' => Currency::CUR_NOK,
            'Couronne thèque' => Currency::CUR_DKK,
            'Forint hongrois' => Currency::CUR_HUF,
            'Zloty polonais' => Currency::CUR_PLN,
            'Dollar australien' => Currency::CUR_AUD,
            'Dollar néo-zélandais' => Currency::CUR_NZD,
            'Rand sud-africain' => Currency::CUR_ZAR,
            'Roupie Indienne' => Currency::CUR_INR,
            'Baht thailandais' => Currency::CUR_THB,
            'Real brésilien' => Currency::CUR_BRL,
            'Dollar singapourien' => Currency::CUR_SGD,
            'Nouvelle livre turque' => Currency::CUR_TRY,
            'Nouveau Shekel' => Currency::CUR_ILS,
            'Won Coréen' => Currency::CUR_KRW,
            'Dollar Hong Kong' => Currency::CUR_HKD,
            'Ryal Saudien' => Currency::CUR_SAR,
            'Dinar Koweitien' => Currency::CUR_KWD,
        ];

        return $currencyList[$name] ?? $name;
    }

    public function fullName(): string
    {
        return 'BCEAO | Banque Centrale des Etats de l’Afrique de l’Ouest';
    }

    public function homeUrl(): string
    {
        return 'https://www.bceao.int';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.bceao.frequency');
    }
}
