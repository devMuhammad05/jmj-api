<?php

namespace App\Models;

use App\Enums\AnnouncementTarget;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    /** @use HasFactory<\Database\Factories\AnnouncementFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'target_audience' => AnnouncementTarget::class,
            'sent_at' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }
}
