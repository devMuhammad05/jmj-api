<?php

namespace App\Filament\Admin\Resources\PoolInvestments\Pages;

use App\Filament\Admin\Resources\PoolInvestments\PoolInvestmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPoolInvestments extends ListRecords
{
    protected static string $resource = PoolInvestmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
