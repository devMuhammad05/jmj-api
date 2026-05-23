<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    /** @use HasFactory<\Database\Factories\AppSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'app_name',
        'support_email',
        'support_phone',
        'support_whatsapp',
        'address',
        'logo_path',
        'favicon_path',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'deriv_referral_url',
        'youtube_tutorials_url',
        'results_timeline',
        'minimum_deposit',
        'maximum_deposit',
        'maintenance_mode',
        'maintenance_message',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
            'minimum_deposit' => 'decimal:2',
            'maximum_deposit' => 'decimal:2',
        ];
    }

    public static function getSettings(): self
    {
        return static::firstOrCreate([], ['app_name' => config('app.name')]);
    }
}
