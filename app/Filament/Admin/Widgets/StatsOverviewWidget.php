<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\Role;
use App\Enums\SignalStatus;
use App\Enums\VerificationStatus;
use App\Models\MetaTraderCredential;
use App\Models\Signal;
use App\Models\User;
use App\Models\Verification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers = User::where('role', Role::User)->count();
        $usersThisMonth = User::where('role', Role::User)
            ->whereMonth('created_at', now()->month)
            ->count();

        $pendingKyc = Verification::where('status', VerificationStatus::PENDING)->count();
        $approvedKyc = Verification::where('status', VerificationStatus::APPROVED)->count();

        $activeMtAccounts = MetaTraderCredential::count();
        $mtAccountsThisMonth = MetaTraderCredential::whereMonth('created_at', now()->month)->count();

        $activeSignals = Signal::where('status', SignalStatus::ACTIVE)->count();
        $signalsThisMonth = Signal::whereMonth('created_at', now()->month)->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description($usersThisMonth . ' new this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 25, $totalUsers]),

            Stat::make('Pending KYC', $pendingKyc)
                ->description($approvedKyc . ' approved total')
                ->descriptionIcon('heroicon-m-document-check')
                ->color($pendingKyc > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.verifications.index', [
                    'tableFilters' => ['status' => ['value' => 'pending']]
                ])),

            Stat::make('MT Accounts', $activeMtAccounts)
                ->description($mtAccountsThisMonth . ' added this month')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info')
                ->chart([5, 8, 12, 15, 18, 20, $activeMtAccounts]),

            Stat::make('Active Signals', $activeSignals)
                ->description($signalsThisMonth . ' created this month')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('primary')
                ->url(route('filament.admin.resources.signals.index')),
        ];
    }
}
