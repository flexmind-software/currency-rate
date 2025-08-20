<?php

namespace FlexMindSoftware\CurrencyRate\DTO;

class CurrencyRateData
{
    public function __construct(
        public string $driver,
        public string $code,
        public string $date,
        public float $rate,
        public float $multiplier = 1,
        public ?string $no = null,
    ) {
    }

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
