<?php

namespace App\Filament\Admin\Resources\Pools\Widgets;

use App\Enums\PoolStatus;
use App\Models\Pool;
use App\Models\PoolInvestment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PoolStatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $total = Pool::count();
        $active = Pool::where('status', PoolStatus::ACTIVE)->count();
        $totalCapital = Pool::where('status', PoolStatus::ACTIVE)->sum('total_amount');
        $totalInvestors = PoolInvestment::where('status', 'verified')->count();

        return [
            Stat::make('Total Pools', number_format($total))
                ->description($active.' currently active')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color('primary'),

            Stat::make('Total Capital', '$'.number_format($totalCapital, 2))
                ->description('Across all active pools')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Investors', number_format($totalInvestors))
                ->description('Verified across all pools')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
