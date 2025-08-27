<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DOMElement;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
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
     * @var CurrencyCode
     */
    public CurrencyCode $currency = CurrencyCode::XOF;
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
                    $this->date = $this->date->sub(DateInterval::createFromDateString('1 day'));
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
            'Couronne danoise' => CurrencyCode::DKK->value,
            'Couronne suédoise' => CurrencyCode::SEK->value,
            'Couronne norvégienne' => CurrencyCode::NOK->value,
            'Couronne thèque' => CurrencyCode::DKK->value,
            'Forint hongrois' => CurrencyCode::HUF->value,
            'Zloty polonais' => CurrencyCode::PLN->value,
            'Dollar australien' => CurrencyCode::AUD->value,
            'Dollar néo-zélandais' => CurrencyCode::NZD->value,
            'Rand sud-africain' => CurrencyCode::ZAR->value,
            'Roupie Indienne' => CurrencyCode::INR->value,
            'Baht thailandais' => CurrencyCode::THB->value,
            'Real brésilien' => CurrencyCode::BRL->value,
            'Dollar singapourien' => CurrencyCode::SGD->value,
            'Nouvelle livre turque' => CurrencyCode::TRY->value,
            'Nouveau Shekel' => CurrencyCode::ILS->value,
            'Won Coréen' => CurrencyCode::KRW->value,
            'Dollar Hong Kong' => CurrencyCode::HKD->value,
            'Ryal Saudien' => CurrencyCode::SAR->value,
            'Dinar Koweitien' => CurrencyCode::KWD->value,
        ];

        return $currencyList[$name] ?? $name;
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return 'BCEAO | Banque Centrale des Etats de l’Afrique de l’Ouest';
    }

    /**
     * @return string
     */
    public function homeUrl(): string
    {
        return 'https://www.bceao.int';
    }

    /**
     * @return string
     */
    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.bceao.frequency');
    }
}
