<?php

namespace App\Filament\Admin\Resources\ProfitDistributions\Pages;

use App\Filament\Admin\Resources\ProfitDistributions\ProfitDistributionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProfitDistributions extends ListRecords
{
    protected static string $resource = ProfitDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
