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
        'driver'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];

    public function getTable()
    {
        return config('currency-rate.table-name');
    }

    public function getCalculateRateAttribute($value)
    {
        return $this->rate * $this->multiplier;
    }
}
