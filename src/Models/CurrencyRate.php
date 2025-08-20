<?php

namespace FlexMindSoftware\CurrencyRate\Models;

use FlexMindSoftware\CurrencyRate\DTO\CurrencyRateData;
use FlexMindSoftware\CurrencyRate\Events\CurrencyRateSaved;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $table = 'currency_rates';

    /**
     * @var array
     */
    protected $casts = [
        'rate' => 'float',
        'multiplier' => 'float',
    ];

    protected $fillable = [
        'code',
        'date',
        'rate',
        'multiplier',
        'driver',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'date',
        'created_at',
        'updated_at',
    ];

    /**
     * @param CurrencyRateData[] $data
     * @param string $connection
     */
    public static function saveIn(array $data, string $connection = 'default')
    {
        if ($data) {
            $columns = ['driver', 'code', 'date', 'no'];
            $chunks = array_chunk($data, 50);

            if ($connection == 'default') {
                $connection = config('database.default');
            }

            foreach ($chunks as $chunk) {
                $mapped = array_map(function ($item) {
                    return $item instanceof CurrencyRateData ? $item->toArray() : $item;
                }, $chunk);

                static::on($connection)
                    ->upsert($mapped, $columns, ['rate', 'no', 'multiplier']);

                event(new CurrencyRateSaved($mapped));
            }
        }
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return config('currency-rate.table-name');
    }

    /**
     * @param mixed $value
     * @return float
     */
    public function getCalculateRateAttribute($value)
    {
        return $this->rate * $this->multiplier;
    }

    /**
     * @param string $value
     * @return void
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }
}
