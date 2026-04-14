<?php

namespace App\Models;

use App\Enums\ProfitDistributionStatus;
use App\Observers\ProfitDistributionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(ProfitDistributionObserver::class)]
class ProfitDistribution extends Model
{
    /** @use HasFactory<\Database\Factories\ProfitDistributionFactory> */
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pool_investment_id',
        'distribution_date',
        'profit_amount',
        'pool_return',
        'status',
        'processed_at',
        'failure_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'distribution_date' => 'date',
            'profit_amount' => 'decimal:2',
            'pool_return' => 'decimal:2',
            'processed_at' => 'datetime',
            'status' => ProfitDistributionStatus::class,
        ];
    }

    /**
     * Get the pool investment that owns this distribution.
     */
    public function poolInvestment(): BelongsTo
    {
        return $this->belongsTo(PoolInvestment::class);
    }

    /**
     * Get the options for logging activity.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['pool_investment_id', 'profit_amount', 'pool_return', 'status', 'processed_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Profit Distribution {$eventName}")
            ->useLogName('profit_distribution');
    }
}
