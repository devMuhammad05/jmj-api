<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    protected $fillable = ['name', 'code', 'is_active', 'config', 'wallet_address', 'network', 'bar_code_path', 'bank_name', 'account_name', 'account_number'];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
