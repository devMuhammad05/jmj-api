<?php

namespace App\Filament\Admin\Resources\Pools\Pages;

use App\Filament\Admin\Resources\Pools\PoolResource;
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
}
