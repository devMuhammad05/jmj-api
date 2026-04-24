<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    /**
     * Retrieve a rate value by key.
     */
    public static function getByKey(string $key): ?float
    {
        return static::where('key', $key)->value('value');
    }
}
