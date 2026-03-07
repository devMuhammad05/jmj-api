<?php

namespace App\Policies;

use App\Enums\VerificationStatus;
use App\Models\User;
use App\Models\Verification;

class VerificationPolicy
{
    /**
     * Determine whether the user can submit (create or update) a verification.
     */
    public function submit(User $user): bool
    {
        $verification = $user->verification;

        if (! $verification) {
            return true;
        }

        return $verification->status === VerificationStatus::REJECTED;
    }

    /**
     * Determine whether the user can view the verification.
     */
    public function view(User $user, Verification $verification): bool
    {
        return $user->id === $verification->user_id;
    }
}
