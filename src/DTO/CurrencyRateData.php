<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\DTO;

class CurrencyRateData
{
    /**
     * @param string $driver
     * @param string $code
     * @param string $date
     * @param float $rate
     * @param float $multiplier
     * @param string|null $no
     */
    public function __construct(
        public string $driver,
        public string $code,
        public string $date,
        public float $rate,
        public float $multiplier = 1,
        public ?string $no = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'driver' => $this->driver,
            'code' => strtoupper($this->code),
            'date' => $this->date,
            'rate' => $this->rate,
            'multiplier' => $this->multiplier,
            'no' => $this->no,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            driver: $data['driver'] ?? '',
            code: $data['code'] ?? '',
            date: $data['date'] ?? '',
            rate: (float) ($data['rate'] ?? 0),
            multiplier: (float) ($data['multiplier'] ?? 1),
            no: $data['no'] ?? null,
        );
    }
}
