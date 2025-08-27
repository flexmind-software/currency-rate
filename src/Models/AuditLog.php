<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'audit_logs';

    protected $fillable = [
        'currency_code',
        'old_rate',
        'new_rate',
        'changed_at',
    ];

    protected $casts = [
        'old_rate' => 'float',
        'new_rate' => 'float',
        'changed_at' => 'datetime',
    ];
}
