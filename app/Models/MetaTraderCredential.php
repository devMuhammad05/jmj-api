<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaTraderCredential extends Model
{
    /** @use HasFactory<\Database\Factories\MetaTraderCredentialFactory> */
    use HasFactory;

    /**
     * Get the client that owns the credential.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
