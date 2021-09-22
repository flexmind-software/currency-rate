<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;

class SerbiaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.nbs.rs/kursnaListaModul/naZeljeniDan.faces';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'serbia';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_SEK;

    /**
     * @var string
     */
    private string $csvPlain;

    /**
     * @var CookieJar
     */
    private CookieJar $cookies;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $params = $this->postParam($date);
        $cookie = $this->cookies->getCookieByName('JSESSIONID');
        $respond = Http::asForm()
            ->withCookies([$cookie->getName() => $cookie->getValue()], $cookie->getDomain())
            ->post(static::URI, $params);
        if ($respond->ok()) {
            $this->csvPlain = $respond->body();
            $this->parseResponse();
            $this->saveInDatabase(true);
        }
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function postParam(DateTime $date): array
    {
        return [
            'index' => 'index',
            'index:brKursneListe' => '',
            'index:yearInner' => $date->format('Y'),
            'index:inputCalendar1' => $date->format('d/m/Y'),
            'index:vrstaInner' => 1,
            'index:prikazInner' => 1, // CSV
            'index:buttonShow' => '',
            'javax.faces.ViewState' => $this->getFormCsrfToken(),
        ];
    }

    /**
     * Get NBS's form CSRF token.
     *
     * @return string CSRF token.
     *
     * @throws \RuntimeException When API is changed.
     */
    private function getFormCsrfToken(): ?string
    {
        $respond = Http::get(static::URI);
        if ($respond->ok()) {
            $response = $respond->body();
            $this->cookies = $respond->cookies();
            libxml_use_internal_errors(true);

            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->loadHTML($response);
            $xpath = new DOMXpath($dom);

            libxml_clear_errors();
            $hiddenInput = $xpath->query('//input[@type="hidden"]');

            /**
             * @var \DOMElement $hidden
             */
            foreach ($hiddenInput as $hidden) {
                if ($hidden->getAttribute('name') === 'javax.faces.ViewState') {
                    return $hidden->getAttribute('value');
                }
            }

        }

        return null;
    }

    private function parseResponse()
    {
        $explode = explode("\n", $this->csvPlain);

        $rateList = array_map(function ($item) {
            return explode(',', $item);
        }, $explode);

        $rateList = array_filter($rateList, function ($item) {
            return count($item) === 8;
        });

        $this->data = [];
        foreach ($rateList as $i => $value) {
            if ($i === 0) {
                continue;
            }

            $rate = ($this->stringToFloat($value[6]) + $this->stringToFloat($value[7])) / 2;

            $this->data[] = [
                'no' => $value[0],
                'code' => $value[4],
                'date' => DateTime::createFromFormat('d.m.Y', $value[1])->format('Y-m-d'),
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat($value[5]),
                'rate' => $rate,
            ];
        }
    }

    public function fullName(): string
    {
        return 'Narodna banka Srbije';
    }

    public function homeUrl(): string
    {
        return 'https://www.nbs.rs/';
    }

    public function infoAboutFrequency(): string
    {
        return 'Queries may be submitted for the period after 15 May 2002. As of 8 August 2006 exchange rate ' .
            'list shall be applicable from 8 a.m. on the selected day until 8 a.m. on the next day (as provided ' .
            'by the Conditions and Manner of Operation of the Foreign Exchange Market). Exchange rate list as of ' .
            'the preceding business day shall be applicable on weekends and holidays.';
    }
}
