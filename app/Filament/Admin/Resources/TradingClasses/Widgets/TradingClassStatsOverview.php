<?php

namespace App\Filament\Admin\Resources\TradingClasses\Widgets;

use App\Models\TradingClass;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TradingClassStatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $total = TradingClass::count();
        $published = TradingClass::where('is_published', true)->count();
        $upcoming = TradingClass::where('is_published', true)
            ->where('scheduled_at', '>', now())
            ->count();

        $publishedPct = $total > 0 ? number_format(($published / $total) * 100, 1) : '0.0';

        return [
            Stat::make('Total Classes', number_format($total))
                ->description('All trading classes')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Published', number_format($published))
                ->description($publishedPct.'% of all classes')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Upcoming', number_format($upcoming))
                ->description('Scheduled in the future')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
