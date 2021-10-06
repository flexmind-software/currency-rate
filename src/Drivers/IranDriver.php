<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Support\Facades\Http;

class IranDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.cbi.ir/exratesadv/exratesadv_en.aspx';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'iran';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_IRR;
    /**
     * @var array
     */
    private array $inputs = [];
    /**
     * @var string
     */
    private string $xml;
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
        $this->getParamArray();
        $this->queryString($date);

        $cookiesList = $this->cookies;

        $respond = Http::asMultipart()
            ->withOptions(['cookies' => $cookiesList])
            ->post(static::URI, $this->inputs);

        if ($respond->ok()) {
            echo $respond->body();
//            $this->xml = $respond->body();
//            $this->parseResponse();
//            $this->saveInDatabase();
        }
    }

    private function getParamArray()
    {
        $respond = Http::asForm()->post(static::URI, $this->queryString(new DateTime()));
        $response = $respond->body();
        if ($respond->ok()) {
            $this->inputs = [];
            $this->cookies = $respond->cookies();
            $xpath = $this->htmlParse($response);
            $hiddenInput = $xpath->query('//input|//textarea|//select');

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
        $params = [
            '__EVENTTARGET'  => '',
            '__EVENTARGUMENT' => '',
            'ctl00$ucBody$ucContent$ctl00$ucForm$Output' => 'rdbXML',
            'ctl00$ucBody$ucContent$ctl00$ucForm$chkSummary' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$ddlWeeks' => 0,
            'ctl00$ucBody$ucContent$ctl00$ucForm$ddlStartMonth' => $date->format('n'),
            'ctl00$ucBody$ucContent$ctl00$ucForm$ddlStartDay' => $date->format('j'),
            'ctl00$ucBody$ucContent$ctl00$ucForm$ddlStartYear' => $date->format('Y'),
            'ctl00$ucBody$ucContent$ctl00$ucForm$ddlEndMonth' => '', // $date->format('n'),
            'ctl00$ucBody$ucContent$ctl00$ucForm$ddlEndDay' => '', // $date->format('j'),
            'ctl00$ucBody$ucContent$ctl00$ucForm$ddlEndYear' => '', // $date->format('Y'),
            'ctl00$ucBody$ucContent$ctl00$ucForm$btnShowRateByDate' => 'Show Rates',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl00$chkSelectAll' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl01$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl01$hdnCurrencyID' => 1,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl02$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl02$hdnCurrencyIDAlt' => 2,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl03$hdnCurrencyID' => 3,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl04$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl04$hdnCurrencyIDAlt' => 4,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl05$hdnCurrencyID' => 5,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl06$hdnCurrencyIDAlt' => 6,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl07$hdnCurrencyID' => 7,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl08$hdnCurrencyIDAlt' => 8,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl09$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl09$hdnCurrencyID' => 9,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl10$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl10$hdnCurrencyIDAlt' => 10,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl11$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl11$hdnCurrencyID' => 11,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl12$hdnCurrencyIDAlt' => 12,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl13$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl13$hdnCurrencyID' => 13,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl14$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl14$hdnCurrencyIDAlt' => 14,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl15$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl15$hdnCurrencyID' => 17,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl16$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl16$hdnCurrencyIDAlt' => 22,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl17$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl17$hdnCurrencyID' => 23,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl18$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl18$hdnCurrencyIDAlt' => 24,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl19$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl19$hdnCurrencyID' => 26,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl20$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl20$hdnCurrencyIDAlt' => 31,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl21$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl21$hdnCurrencyID' => 32,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl22$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl22$hdnCurrencyIDAlt' => 37,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl23$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl23$hdnCurrencyID' => 38,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl24$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl24$hdnCurrencyIDAlt' => 39,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl25$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl25$hdnCurrencyID' => 41,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl26$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl26$hdnCurrencyIDAlt' => 43,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl27$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl27$hdnCurrencyID' => 45,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl28$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl28$hdnCurrencyIDAlt' => 56,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl29$hdnCurrencyID' => 57,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl30$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl30$hdnCurrencyIDAlt' => 59,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl31$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl31$hdnCurrencyID' => 62,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl32$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl32$hdnCurrencyIDAlt' => 63,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl33$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl33$hdnCurrencyID' => 64,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl34$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl34$hdnCurrencyIDAlt' => 65,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl35$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl35$hdnCurrencyID' => 66,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl36$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl36$hdnCurrencyIDAlt' => 67,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl37$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl37$hdnCurrencyID' => 71,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl38$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl38$hdnCurrencyIDAlt' => 72,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl39$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl39$hdnCurrencyID' => 82,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl40$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl40$hdnCurrencyIDAlt' => 84,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl41$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl41$hdnCurrencyID' => 85,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl42$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl42$hdnCurrencyIDAlt' => 88,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl43$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl43$hdnCurrencyID' => 89,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl44$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl44$hdnCurrencyIDAlt' => 90,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl45$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl45$hdnCurrencyID' => 91,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl46$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl46$hdnCurrencyIDAlt' => 94,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl47$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl47$hdnCurrencyID' => 98,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl48$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl48$hdnCurrencyIDAlt' => 110,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl49$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl49$hdnCurrencyID' => 119,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl50$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl50$hdnCurrencyIDAlt' => 131,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl51$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl51$hdnCurrencyID' => 136,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl52$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl52$hdnCurrencyIDAlt' => 139,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl53$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl53$hdnCurrencyID' => 142,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl54$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl54$hdnCurrencyIDAlt' => 181,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl55$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl55$hdnCurrencyID' => 198,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl56$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl56$hdnCurrencyIDAlt' => 209,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl57$chkCurrency' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl57$hdnCurrencyID' => 218,
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl58$chkCurrencyAlt' => 'on',
            'ctl00$ucBody$ucContent$ctl00$ucForm$rptCurrency$ctl58$hdnCurrencyIDAlt' => 229,
        ];

        foreach ($params as $key => $param) {
            $this->inputs[$key] = $param;
        }

        return $params;
    }

    private function parseResponse()
    {
        echo $this->xml;

        die();
        $xmlElement = simplexml_load_string($this->xml, "SimpleXMLElement", LIBXML_NOCDATA);
        dd($xmlElement);
    }

    public function fullName(): string
    {
        return 'Bank Markazi-ye Jomhuri-ye Eslāmi-ye Irān';
    }

    public function homeUrl(): string
    {
        return 'https://www.cbi.ir/default_en.aspx';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
