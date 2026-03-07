<?php

namespace App\Policies;

use App\Enums\VerificationStatus;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Auth\Access\Response;

class VerificationPolicy
{
    /**
     * Determine whether the user can submit (create or update) a verification.
     */
    public function submit(User $user): Response
    {
        $verification = $user->verification;

        if (! $verification) {
            return Response::allow();
        }

        if ($verification->status === VerificationStatus::APPROVED) {
            return Response::deny('Your account is already verified.');
        }

        if ($verification->status === VerificationStatus::PENDING) {
            return Response::deny('Your verification is currently under review.');
        }

        return $verification->status === VerificationStatus::REJECTED
            ? Response::allow()
            : Response::deny('You cannot submit verification data at this time.');
    }

    /**
     * Determine whether the user can view the verification.
     */
    public function view(User $user, Verification $verification): bool
    {
        return $user->id === $verification->user_id;
    }
}
