<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Str;

final class UserObserver
{
    public function creating(User $user): void
    {
        if (empty($user->referral_code)) {
            do {
                $code = strtoupper(Str::random(8));
            } while (User::where('referral_code', $code)->exists());

            $user->referral_code = $code;
        }
    }
}
