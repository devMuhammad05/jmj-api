<?php

namespace App\Filament\Admin\Resources\ProfitDistributions\Pages;

use App\Filament\Admin\Resources\ProfitDistributions\ProfitDistributionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProfitDistribution extends EditRecord
{
    protected static string $resource = ProfitDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
