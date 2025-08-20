<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTime;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\DriverMetadata;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;

abstract class BaseDriver implements DriverMetadata
{
    use HttpFetcher;

    /**
     * @const string
     */
    public const DRIVER_NAME = '';

    /**
     * @const string
     */
    public const URI = '';
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

    public function __construct()
    {
        $this->config = config('currency-rate');
        $this->lastDate = CurrencyRate::where('driver', static::DRIVER_NAME)->latest('date')->value('date');
    }

    /**
     * @param DateTime $date
     *
     * @return $this
     */
    public function setLastDataTime(DateTime $date): self
    {
        $this->lastDate = $date;

        return $this;
    }

    /**
     * @param DateTime $date
     *
     * @return $this
     */
    public function setDataTime(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return array
     */
    public function retrieveData(): array
    {
        return $this->data;
    }

    /**
     * Return driver unique name.
     */
    public function driverName(): string
    {
        return static::DRIVER_NAME;
    }

    /**
     * Return driver base URI.
     */
    public function uri(): string
    {
        return static::URI;
    }

    protected function saveInDatabase()
    {
        if ($this->data) {
            $columns = ['driver', 'code', 'date', 'no'];
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

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html ?? $this->html);
        $xpath = new DOMXpath($dom);

        libxml_clear_errors();

        return $xpath;
    }

    /**
     * Extract rate data by date
     * If the date does not exist we force set latest data
     */
    protected function findByDate(string $label, string $dateFormat = 'Y-m-d')
    {
        if (! $this->date) {
            ! $this->data ?: $this->data = reset($this->data);
        }

        $formatDate = $this->date->format($dateFormat);

        foreach ($this->data ?? [] as $data) {
            if (empty($data[$label]) || $data[$label] !== $formatDate) {
                continue;
            }

            $this->data = $data;
        }
    }
}
