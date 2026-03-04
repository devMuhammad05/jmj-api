<?php

namespace App\Models;

use App\Enums\IdType;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verification extends Model
{
    /** @use HasFactory<\Database\Factories\VerificationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_type' => IdType::class,
            'status' => VerificationStatus::class,
        ];
    }

    /**
     * Get the client that owns the verification.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
