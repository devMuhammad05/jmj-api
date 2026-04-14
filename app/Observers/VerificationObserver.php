<?php

namespace App\Observers;

use App\Enums\VerificationStatus;
use App\Models\Verification;
use App\Notifications\User\AccountVerifiedNotification;
use App\Notifications\User\KycRejectedNotification;

class VerificationObserver
{
    /**
     * Handle the Verification "updated" event.
     */
    public function updated(Verification $verification): void
    {
        if (! $verification->wasChanged('status')) {
            return;
        }

        if ($verification->status === VerificationStatus::APPROVED) {
            $verification->user->notify(new AccountVerifiedNotification);
        }

        if ($verification->status === VerificationStatus::REJECTED) {
            $verification->user->notify(new KycRejectedNotification($verification));
        }
    }
}
