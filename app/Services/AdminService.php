<?php

declare(strict_types=1);

namespace App\Services;

class AdminService
{
    /** @return array<int, string> */
    public function getAdminEmails(): array
    {
        return config('app.admin_emails', []);
    }
}
