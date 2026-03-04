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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'mt_account_number',
        'mt_password',
        'mt_server',
        'initial_deposit',
        'risk_level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'mt_password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'risk_level' => \App\Enums\RiskLevel::class,
            'initial_deposit' => 'decimal:2',
        ];
    }

    /**
     * Get the client that owns the credential.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
