<?php

namespace App\Observers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Notifications\User\PaymentRejectedNotification;

class PaymentObserver
{
    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        if (! $payment->wasChanged('status')) {
            return;
        }

        if ($payment->status === PaymentStatus::Rejected) {
            $payment->loadMissing(['user', 'plan']);
            $payment->user->notify(new PaymentRejectedNotification($payment));
        }
    }
}
