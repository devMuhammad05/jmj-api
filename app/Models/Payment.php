<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Observers\PaymentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(PaymentObserver::class)]
class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'pool_investment_id',
        'meta_trader_credential_id',
        'payment_gateway_id',
        'amount',
        'status',
        'type',
        'reference',
        'transaction_id',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function poolInvestment(): BelongsTo
    {
        return $this->belongsTo(PoolInvestment::class);
    }

    public function metaTraderCredential(): BelongsTo
    {
        return $this->belongsTo(MetaTraderCredential::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    public function proofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }
}
