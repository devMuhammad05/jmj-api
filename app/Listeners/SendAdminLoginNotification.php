<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\Role;
use App\Models\User;
use App\Notifications\Admin\AdminLoginNotification;
use Illuminate\Auth\Events\Login;

class SendAdminLoginNotification
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! ($user instanceof User) || $user->role !== Role::Admin) {
            return;
        }

        $request = request();

        $user->notify(new AdminLoginNotification(
            ipAddress: $request->ip() ?? 'Unknown',
            userAgent: $request->userAgent() ?? 'Unknown',
            loginAt: now()->toDateTimeString(),
        ));
    }
}
