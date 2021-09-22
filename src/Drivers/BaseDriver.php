<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;

abstract class BaseDriver
{
    /**
     * @var DateTime
     */
    protected DateTime $date;
    /**
     * @var array
     */
    protected array $config;

    /**
     * @var array
     */
    protected array $data = [];
    /**
     * @var DateTime|null
     */
    protected ?DateTime $lastDate;

    public function __construct()
    {
        $this->config = config('currency-rate');
        $this->lastDate = CurrencyRate::where('driver', static::DRIVER_NAME)->latest('date')->value('date');
    }

    /**
     * @param bool $checkNo
     */
    protected function saveInDatabase(bool $checkNo = false)
    {
        if ($this->data) {
            $columns = ['driver', 'code', 'date'];
            if ($checkNo) {
                $columns[] = 'no';
            }
            CurrencyRate::upsert($this->data, $columns, ['rate', 'multiplier']);
        }
    }

    /**
     * @param string|null $string
     *
     * @return string|null
     */
    protected function clearRow(?string $string): ?string
    {
        return preg_replace('~[\r\n]+~', '', trim($string));
    }

    /**
     * @param string $string
     *
     * @return float
     */
    protected function stringToFloat(string $string): float
    {
        return (float)str_replace(',', '.', $string);
    }

    /**
     * @param string $html
     *
     * @return DOMXPath
     */
    protected function htmlParse(string $html): DOMXPath
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);
        $xpath = new DOMXpath($dom);

        libxml_clear_errors();

        return $xpath;
    }
}
