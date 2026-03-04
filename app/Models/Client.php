<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    /**
     * Get the user that owns the client.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the verification associated with the client.
     */
    public function verification(): HasOne
    {
        return $this->hasOne(Verification::class);
    }

    /**
     * Get the MetaTrader credential associated with the client.
     */
    public function metaTraderCredential(): HasOne
    {
        return $this->hasOne(MetaTraderCredential::class);
    }
}
