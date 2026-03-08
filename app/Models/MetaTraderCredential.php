<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MetaTraderCredential extends Model
{
    /** @use HasFactory<\Database\Factories\MetaTraderCredentialFactory> */
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'mt_account_number',
        'mt_password',
        'mt_server',
        'platform_type',
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
            'platform_type' => \App\Enums\MetaTraderPlatformType::class,
            'initial_deposit' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the credential.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['mt_account_number', 'mt_server', 'platform_type', 'risk_level', 'initial_deposit'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
