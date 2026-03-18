<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaAccountMetric extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'balance',
        'equity',
        'profit',
        'deposits',
        'withdrawals',
        'margin',
        'free_margin',
        'trades',
        'profit_factor',
        'sharpe_ratio',
        'won_trades_percent',
        'lost_trades_percent',
        'daily_growth',
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
            'profit' => 'decimal:2',
            'deposits' => 'decimal:2',
            'withdrawals' => 'decimal:2',
            'margin' => 'decimal:2',
            'free_margin' => 'decimal:2',
            'profit_factor' => 'decimal:2',
            'sharpe_ratio' => 'decimal:2',
            'won_trades_percent' => 'decimal:2',
            'lost_trades_percent' => 'decimal:2',
            'daily_growth' => 'array',
        ];
    }
}
