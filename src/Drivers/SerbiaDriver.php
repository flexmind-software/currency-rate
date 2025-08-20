<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
use DOMElement;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;
use RuntimeException;

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
    public CurrencyCode $currency = CurrencyCode::RSD;

    /**
     * @var string
     */
    private string $csvPlain;

    /**
     * @var CookieJar
     */
    private CookieJar $cookies;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $params = $this->postParam();
        $cookie = $this->cookies->getCookieByName('JSESSIONID');
        $respond = Http::asForm()
            ->withCookies([$cookie->getName() => $cookie->getValue()], $cookie->getDomain())
            ->post(static::URI, $params);
        if ($respond->ok()) {
            $this->csvPlain = $respond->body();
            $this->parseResponse();
            $this->saveInDatabase(true);
        }

        return $this;
    }

    /**
     * @return array
     */
    private function postParam(): array
    {
        return [
            'index' => 'index',
            'index:brKursneListe' => '',
            'index:yearInner' => $this->date->format('Y'),
            'index:inputCalendar1' => $this->date->format('d/m/Y'),
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
     * @throws RuntimeException When API is changed.
     */
    private function getFormCsrfToken(): ?string
    {
        $respond = Http::get(static::URI);
        if ($respond->ok()) {
            $response = $respond->body();
            $this->cookies = $respond->cookies();

            $xpath = $this->htmlParse($response);

            $hiddenInput = $xpath->query('//input[@type="hidden"]');

            /**
             * @var DOMElement $hidden
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
                'date' => DateTimeImmutable::createFromFormat('d.m.Y', $value[1])->format('Y-m-d'),
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
        return __('currency-rate::description.serbia.frequency');
    }
}
