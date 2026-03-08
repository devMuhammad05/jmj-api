<?php

namespace App\Models;

use App\Enums\PoolInvestmentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PoolInvestment extends Model
{
    /** @use HasFactory<\Database\Factories\PoolInvestmentFactory> */
    use HasFactory, HasUuids, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'pool_id',
        'full_name',
        'phone_number',
        'bank_name',
        'account_number',
        'account_name',
        'contribution',
        'share_percentage',
        'payment_proof_path',
        'status',
        'terms_accepted',
        'verified_at',
        'rejection_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'contribution' => 'decimal:2',
            'share_percentage' => 'decimal:4',
            'terms_accepted' => 'boolean',
            'verified_at' => 'datetime',
            'status' => PoolInvestmentStatus::class,
        ];
    }

    /**
     * Get the user that owns the pool investment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pool that this investment belongs to.
     */
    public function pool(): BelongsTo
    {
        return $this->belongsTo(Pool::class);
    }

    /**
     * Get the profit distributions for this investment.
     */
    public function profitDistributions(): HasMany
    {
        return $this->hasMany(ProfitDistribution::class);
    }

    /**
     * Get the options for logging activity.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'pool_id', 'contribution', 'share_percentage', 'status', 'verified_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Pool Investment {$eventName}")
            ->useLogName('pool_investment');
    }
}
