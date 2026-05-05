<?php

namespace App\Models;

use App\Enums\GatewayType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'payment_type' => GatewayType::class,
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
