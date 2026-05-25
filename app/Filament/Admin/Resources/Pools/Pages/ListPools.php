<?php

namespace App\Filament\Admin\Resources\Pools\Pages;

use App\Filament\Admin\Resources\Pools\PoolResource;
use App\Filament\Admin\Resources\Pools\Widgets\PoolCapitalChart;
use App\Filament\Admin\Resources\Pools\Widgets\PoolsByStatusChart;
use App\Filament\Admin\Resources\Pools\Widgets\PoolStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPools extends ListRecords
{
    protected static string $resource = PoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PoolStatsOverview::class,
            PoolsByStatusChart::class,
            PoolCapitalChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }
}
