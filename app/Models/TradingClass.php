<?php

namespace App\Models;

use App\Enums\ClassPlatform;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingClass extends Model
{
    /** @use HasFactory<\Database\Factories\TradingClassFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'scheduled_at',
        'platform',
        'meeting_link',
        'is_published',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'platform' => ClassPlatform::class,
            'is_published' => 'boolean',
        ];
    }
}
