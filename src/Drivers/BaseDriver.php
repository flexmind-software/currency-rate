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

    /**
     * @var string
     */
    protected string $html;

    /**
     * @var array
     */
    protected array $json;
    /**
     * @var DOMDocument
     */
    protected DOMDocument $dom;

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
            $chunks = array_chunk($this->data, 50);
            foreach ($chunks as $chunk) {
                CurrencyRate::upsert($chunk, $columns, ['rate', 'multiplier']);
            }
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
     * @param string|null $html
     *
     * @return DOMXPath
     */
    protected function htmlParse(?string $html = null): DOMXPath
    {
        libxml_use_internal_errors(true);

        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->loadHTML($html ?? $this->html);
        $xpath = new DOMXpath($this->dom);

        libxml_clear_errors();

        return $xpath;
    }
}
