<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminService
{
    /** @return array<int, string> */
    public function getAdminEmails(): array
    {
        return config('app.admin_emails', []);
    }

    /** @return Collection<int, User> */
    public function getAdmins(): Collection
    {
        return User::query()->where('role', Role::Admin)->get();
    }
}
