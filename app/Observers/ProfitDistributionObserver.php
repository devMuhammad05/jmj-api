<?php

namespace App\Observers;

use App\Enums\ProfitDistributionStatus;
use App\Models\ProfitDistribution;
use App\Notifications\User\ProfitDistributedNotification;

class ProfitDistributionObserver
{
    /**
     * Handle the ProfitDistribution "updated" event.
     */
    public function updated(ProfitDistribution $profitDistribution): void
    {
        if (! $profitDistribution->wasChanged('status')) {
            return;
        }

        if ($profitDistribution->status === ProfitDistributionStatus::PROCESSED) {
            $profitDistribution->loadMissing('poolInvestment.user');
            $profitDistribution->poolInvestment->user->notify(
                new ProfitDistributedNotification($profitDistribution),
            );
        }
    }
}
