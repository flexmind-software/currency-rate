<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
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
    public CurrencyCode $currency = CurrencyCode::MKD;

    /**
     * @var string
     */
    private string $xml;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $client = new SoapClient(static::URI);
        $xml = $client->GetExchangeRate($this->soapParams());
        if (property_exists($xml, 'GetExchangeRateResult')) {
            $this->xml = $xml->GetExchangeRateResult;
            $this->parseResponse();
        }

        return $this;
    }

    /**
     * @return array
     */
    private function soapParams(): array
    {
        return [
            'StartDate' => $this->date->format('d.m.Y'),
            'EndDate' => $this->date->sub(DateInterval::createFromDateString('1 day'))->format('d.m.Y'),
        ];
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
        return 'https://www.nbrm.mk/';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.macedonia.frequency');
    }
}
