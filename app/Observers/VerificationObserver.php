<?php

namespace App\Observers;

use App\Enums\VerificationStatus;
use App\Models\Verification;
use App\Notifications\AccountVerifiedNotification;

class VerificationObserver
{
    /**
     * Handle the Verification "updated" event.
     */
    public function updated(Verification $verification): void
    {
        if ($verification->wasChanged('status') && $verification->status === VerificationStatus::APPROVED) {
            $verification->user->notify(new AccountVerifiedNotification);
        }
    }
}
