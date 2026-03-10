<?php

namespace App\Models;

use App\Enums\PoolStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pool extends Model
{
    /** @use HasFactory<\Database\Factories\PoolFactory> */
    use HasFactory, HasUuids, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "name",
        "total_amount",
        "investor_count",
        "minimum_investment",
        "status",
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "total_amount" => "decimal:2",
            "minimum_investment" => "decimal:2",
            "investor_count" => "integer",
            "status" => PoolStatus::class,
        ];
    }

    /**
     * Get the pool investments for this pool.
     */
    public function poolInvestments(): HasMany
    {
        return $this->hasMany(PoolInvestment::class);
    }

    /**
     * Get the active pool investments for this pool.
     */
    public function activeInvestments(): HasMany
    {
        return $this->hasMany(PoolInvestment::class)->where("status", "active");
    }

    /**
     * Get the options for logging activity.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["name", "total_amount", "investor_count", "status"])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn(string $eventName) => "Pool {$eventName}",
            )
            ->useLogName("pool");
    }
}
