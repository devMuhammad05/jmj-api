<?php

namespace App\Observers;

use App\Enums\PoolInvestmentStatus;
use App\Models\PoolInvestment;
use App\Notifications\User\PoolInvestmentApprovedNotification;
use App\Notifications\User\PoolInvestmentRejectedNotification;

class PoolInvestmentObserver
{
    /**
     * Handle the PoolInvestment "updated" event.
     */
    public function updated(PoolInvestment $poolInvestment): void
    {
        if (! $poolInvestment->wasChanged('status')) {
            return;
        }

        $poolInvestment->loadMissing(['user', 'pool']);

        if ($poolInvestment->status === PoolInvestmentStatus::VERIFIED) {
            $poolInvestment->user->notify(new PoolInvestmentApprovedNotification($poolInvestment));
        }

        if ($poolInvestment->status === PoolInvestmentStatus::REJECTED) {
            $poolInvestment->user->notify(new PoolInvestmentRejectedNotification($poolInvestment));
        }
    }
}
