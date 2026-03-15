<?php

namespace App\Models;

use App\Enums\SignalAction;
use App\Enums\SignalStatus;
use App\Enums\SignalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    /** @use HasFactory<\Database\Factories\SignalFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'symbol',
        'action',
        'type',
        'entry_price',
        'stop_loss',
        'take_profit_1',
        'take_profit_2',
        'take_profit_3',
        'status',
        'pips_result',
        'notes',
        'is_published',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => SignalType::class,
            'status' => SignalStatus::class,
            'action' => SignalAction::class,
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'entry_price' => 'decimal:5',
            'stop_loss' => 'decimal:5',
            'take_profit_1' => 'decimal:5',
            'take_profit_2' => 'decimal:5',
            'take_profit_3' => 'decimal:5',
            'pips_result' => 'decimal:2',
        ];
    }

    /**
     * Get the options for logging activity.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['symbol', 'action', 'status', 'entry_price', 'stop_loss', 'take_profit_1', 'pips_result'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Signal {$eventName}")
            ->useLogName('signal');
    }
}
