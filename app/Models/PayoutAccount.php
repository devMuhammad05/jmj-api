<?php

namespace App\Models;

use App\Enums\PayoutAccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayoutAccount extends Model
{
    /** @use HasFactory<\Database\Factories\PayoutAccountFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'label',
        'is_default',
        'bank_name',
        'account_name',
        'account_number',
        'wallet_address',
        'network',
        'coin',
    ];

    protected function casts(): array
    {
        return [
            'type' => PayoutAccountType::class,
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
