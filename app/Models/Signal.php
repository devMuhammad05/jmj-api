<?php

namespace App\Models;

use App\Enums\SignalAction;
use App\Enums\SignalStatus;
use App\Enums\SignalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    /** @use HasFactory<\Database\Factories\SignalFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => SignalType::class,
            'status' => SignalStatus::class,
            'action' => SignalAction::class,
            'is_published' => 'boolean',
        ];
    }
}
