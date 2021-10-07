<?php

namespace FlexMindSoftware\CurrencyRate\Models;

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
     * @param array $data
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
                static::on($connection)
                    ->upsert($chunk, $columns, ['rate', 'no', 'multiplier']);
            }
        }
    }

    public function getTable()
    {
        return config('currency-rate.table-name');
    }

    public function getCalculateRateAttribute($value)
    {
        return $this->rate * $this->multiplier;
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }
}
