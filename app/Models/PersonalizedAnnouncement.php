<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PersonalizedAnnouncement extends Model
{
    /** @use HasFactory<\Database\Factories\PersonalizedAnnouncementFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }
}
