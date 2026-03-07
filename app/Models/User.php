<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'country',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getFilamentName(): string
    {
        return $this->full_name ?? $this->email ?? 'Administrator';
    }

    public function canAccessPanel(Panel $panel): bool
    {

        // if (! app()->isProduction()) {
        //     return true; // Allow access in non-production environments
        // }

        // if ($this->role === Role::Admin) {
        //     return str_ends_with($this->email, '@jmj.com') && $this->hasVerifiedEmail();
        // }

        // return false;

        return true;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    /**
     * Get the meta trader credentials associated with the user.
     *
     * @return HasMany<MetaTraderCredential, $this>
     */
    public function metaTraderCredentials(): HasMany
    {
        return $this->hasMany(MetaTraderCredential::class);
    }

    /**
     * Get the verification associated with the user.
     */
    public function verification(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Verification::class);
    }
}
