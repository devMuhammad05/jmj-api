<?php

namespace App\Filament\Admin\Resources\TradingClasses\Pages;

use App\Filament\Admin\Resources\TradingClasses\TradingClassResource;
use App\Filament\Admin\Resources\TradingClasses\Widgets\TradingClassesPerMonthChart;
use App\Filament\Admin\Resources\TradingClasses\Widgets\TradingClassPlatformChart;
use App\Filament\Admin\Resources\TradingClasses\Widgets\TradingClassStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTradingClasses extends ListRecords
{
    protected static string $resource = TradingClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TradingClassStatsOverview::class,
            TradingClassesPerMonthChart::class,
            TradingClassPlatformChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }
}
