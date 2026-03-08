<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\PoolInvestment;
use App\Models\User;

class PoolInvestmentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === Role::Admin) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any pool investments.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === Role::Admin;
    }

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
