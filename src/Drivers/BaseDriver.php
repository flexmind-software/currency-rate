<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateTimeImmutable;
use DOMDocument;
use DOMXPath;
use FlexMindSoftware\CurrencyRate\Contracts\DriverMetadata;
use FlexMindSoftware\CurrencyRate\DTO\CurrencyRateData;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;
use FlexMindSoftware\CurrencyRate\Support\Logger;
use Illuminate\Support\Facades\DB;
use Psr\Http\Client\ClientInterface;

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
     * @var DateTimeImmutable
     */
    protected DateTimeImmutable $date;
    /**
     * @var array
     */
    protected array $config;

    /**
     * @var array<int, array|CurrencyRateData>
     */
    protected array $data = [];
    /**
     * @var DateTimeImmutable|null
     */
    protected ?DateTimeImmutable $lastDate;

    /**
     * @var string
     */
    protected string $html;

    /**
     * @var array
     */
    protected array $json;

    /**
     * @param ClientInterface|null $httpClient
     */
    public function __construct(?ClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient;
        $this->config = config('currency-rate');
        $this->lastDate = CurrencyRate::where('driver', static::DRIVER_NAME)->latest('date')->value('date');
    }

    /**
     * @param DateTimeImmutable $date
     *
     * @return $this
     */
    public function setLastDataTime(DateTimeImmutable $date): self
    {
        $clone = clone $this;
        $clone->lastDate = $date;

        return $clone;
    }

    /**
     * @param DateTimeImmutable $date
     *
     * @return $this
     */
    public function setDataTime(DateTimeImmutable $date): self
    {
        $clone = clone $this;
        $clone->date = $date;

        return $clone;
    }

    /**
     * @return CurrencyRateData[]
     */
    public function retrieveData(): array
    {
        $data = is_array($this->data) ? $this->data : [$this->data];

        if (isset($data['code'])) {
            $data = [$data];
        } elseif (array_is_list($data) === false) {
            $data = array_values($data);
        }

        return array_map(function ($item) {
            return $item instanceof CurrencyRateData ? $item : CurrencyRateData::fromArray($item);
        }, $data);
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
            $dataset = $this->data instanceof CurrencyRateData ? [$this->data] : $this->data;
            $chunks = array_chunk($dataset, 50);

            foreach ($chunks as $chunk) {
                $mapped = array_map(function ($item) {
                    return $item instanceof CurrencyRateData ? $item->toArray() : $item;
                }, $chunk);

                try {
                    DB::transaction(function () use ($mapped, $columns) {
                        CurrencyRate::upsert($mapped, $columns, ['rate', 'multiplier']);
                    });
                } catch (\Throwable $e) {
                    Logger::error('CurrencyRate upsert failed', ['exception' => $e]);
                }
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
    protected function stringToFloat(int|float|string $string): float
    {
        return (float) str_replace(',', '.', (string) $string);
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
            $value = $data instanceof CurrencyRateData ? $data->{$label} : ($data[$label] ?? null);

            if ($value !== $formatDate) {
                continue;
            }

            $this->data = [$data];
        }
    }
}
