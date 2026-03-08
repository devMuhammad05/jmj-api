<?php

namespace App\Policies;

use App\Models\ProfitDistribution;
use App\Models\User;

class ProfitDistributionPolicy
{
    /**
     * Determine whether the user can view the profit distribution.
     */
    public function view(User $user, ProfitDistribution $profitDistribution): bool
    {
        return $user->id === $profitDistribution->poolInvestment->user_id;
    }
}
