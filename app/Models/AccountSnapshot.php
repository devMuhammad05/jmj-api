<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountSnapshot extends Model
{
    use HasUuids;

    /** @var bool */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'mt_account_id',
        'balance',
        'equity',
        'margin',
        'free_margin',
        'margin_level',
        'leverage',
        'currency',
        'fetched_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'equity' => 'decimal:2',
            'margin' => 'decimal:2',
            'free_margin' => 'decimal:2',
            'margin_level' => 'decimal:4',
            'fetched_at' => 'datetime',
        ];
    }

    /**
     * Get the MetaTrader credential that owns the snapshot.
     */
    public function metaTraderCredential(): BelongsTo
    {
        return $this->belongsTo(MetaTraderCredential::class, 'mt_account_id');
    }
}
