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
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser, HasName, HasPasskeys
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,
        HasFactory,
        InteractsWithPasskeys,
        Notifiable,
        TwoFactorAuthenticatable;

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
        'pin',
        'pin_set_at',
        'pin_attempts',
        'pin_locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token', 'pin'];

    public function getFilamentName(): string
    {
        return $this->full_name ?? ($this->email ?? 'Administrator');
    }

    /**
     * Get the user's name attribute (alias for full_name).
     */
    public function getNameAttribute(): ?string
    {
        return $this->full_name;
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
            'pin_set_at' => 'datetime',
            'pin_locked_until' => 'datetime',
            'pin_attempts' => 'integer',
        ];
    }

    /**
     * Determine whether the user has configured a PIN.
     */
    public function isPinSet(): bool
    {
        return $this->pin !== null;
    }

    /**
     * Determine whether the user's PIN is currently locked out.
     */
    public function isPinLocked(): bool
    {
        return $this->pin_locked_until !== null &&
            $this->pin_locked_until->isFuture();
    }

    /**
     * Verify a raw 4-digit PIN against the stored hash.
     */
    public function verifyPin(int $raw): bool
    {
        return \Illuminate\Support\Facades\Hash::check(
            (string) $raw,
            $this->pin,
        );
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

    /**
     * Get the pool investments associated with the user.
     *
     * @return HasMany<PoolInvestment, $this>
     */
    public function poolInvestments(): HasMany
    {
        return $this->hasMany(PoolInvestment::class);
    }
}
