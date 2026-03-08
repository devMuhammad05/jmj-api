<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\ProfitDistribution;
use App\Models\User;

class ProfitDistributionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === Role::Admin) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any profit distributions.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can view the profit distribution.
     */
    public function view(User $user, ProfitDistribution $profitDistribution): bool
    {
        return $user->id === $profitDistribution->poolInvestment->user_id;
    }
}
