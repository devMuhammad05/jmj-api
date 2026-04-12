<?php

namespace App\Models;

use App\Enums\PlanType;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    /** @use HasFactory<\Database\Factories\PlanFactory> */
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'duration_days',
        'is_active',
        'level',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'is_active' => 'boolean',
            'level' => 'integer',
            'type' => PlanType::class,
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function tradingClasses(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(TradingClass::class, 'feature', 'plan_features');
    }

    public function signals(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Signal::class, 'feature', 'plan_features');
    }
}
