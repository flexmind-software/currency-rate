<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Models\Currency;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use SoapClient;

class MacedoniaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.nbrm.mk/klservice/kurs.asmx?wsdl';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'macedonia';
    /**
     * @var string
     */
    public string $currency = Currency::CUR_MKD;

    /**
     * @var string
     */
    private string $xml;

    /**
     * @param DateTime $date
     *
     * @return void
     */
    public function downloadRates(DateTime $date)
    {
        $client = new SoapClient(
            static::URI
        );

        $xml = $client->GetExchangeRate(
            [
                'StartDate' => $date->format('d.m.Y'),
                'EndDate' => $date->sub(\DateInterval::createFromDateString('1 day'))->format('d.m.Y')
            ]
        );

        if (property_exists($xml, 'GetExchangeRateResult')) {
            $this->xml = $xml->GetExchangeRateResult;

            $this->parseResponse();
            $this->saveInDatabase(true);
        }
    }

    private function parseResponse()
    {
        $xml = simplexml_load_string($this->xml, "SimpleXMLElement", LIBXML_NOCDATA);

        $this->data = [];
        foreach ($xml->KursZbir as $xmlElement) {
            $this->data[] = [
                'no' => (int)$xmlElement->RBr,
                'code' => (string)$xmlElement->Oznaka,
                'date' => date('Y-m-d', strtotime((string)$xmlElement->Datum)),
                'driver' => static::DRIVER_NAME,
                'multiplier' => $this->stringToFloat((int)$xmlElement->Nomin),
                'rate' => $this->stringToFloat($xmlElement->Sreden),
            ];
        }
    }

    public function fullName(): string
    {
        return 'Narodna Banka na Republika Severna Makedonija';
    }

    public function homeUrl(): string
    {
        return 'http://www.nbrm.mk/';
    }

    public function infoAboutFrequency(): string
    {
        return '';
    }
}
