<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Pool;
use App\Models\User;

class PoolPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === Role::Admin) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any pools.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can view the pool.
     */
    public function view(User $user, Pool $pool): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can create pools.
     */
    public function create(User $user): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can update the pool.
     */
    public function update(User $user, Pool $pool): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can delete the pool.
     */
    public function delete(User $user, Pool $pool): bool
    {
        return $user->role === Role::Admin;
    }
}
