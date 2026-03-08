<?php

namespace App\Policies;

use App\Models\PoolInvestment;
use App\Models\User;

class PoolInvestmentPolicy
{
    /**
     * Determine whether the user can view the pool investment.
     */
    public function view(User $user, PoolInvestment $poolInvestment): bool
    {
        return $user->id === $poolInvestment->user_id;
    }


    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the pool investment.
     */
    public function update(User $user, PoolInvestment $poolInvestment): bool
    {
        return $user->id === $poolInvestment->user_id;
    }

    /**
     * Determine whether the user can delete the pool investment.
     */
    public function delete(User $user, PoolInvestment $poolInvestment): bool
    {
        return $user->id === $poolInvestment->user_id;
    }
}
