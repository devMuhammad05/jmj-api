<?php

namespace App\Filament\Admin\Resources\Users\Widgets;

use App\Enums\Role;
use App\Enums\VerificationStatus;
use App\Models\User;
use App\Models\Verification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalUsers = User::where('role', Role::User)->count();
        $verifiedUsers = User::where('role', Role::User)->whereNotNull('email_verified_at')->count();
        $kycApproved = Verification::where('status', VerificationStatus::APPROVED)->count();

        $verifiedPct = $totalUsers > 0 ? number_format(($verifiedUsers / $totalUsers) * 100, 1) : '0.0';
        $kycPct = $totalUsers > 0 ? number_format(($kycApproved / $totalUsers) * 100, 1) : '0.0';

        $newThisMonth = User::where('role', Role::User)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description($newThisMonth.' new this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Email Verified', number_format($verifiedUsers))
                ->description($verifiedPct.'% of all users')
                ->descriptionIcon('heroicon-m-envelope-open')
                ->color('success'),

            Stat::make('KYC Approved', number_format($kycApproved))
                ->description($kycPct.'% of all users')
                ->descriptionIcon('heroicon-m-identification')
                ->color('info'),
        ];
    }
}
