<?php

namespace App\Filament\Admin\Resources\PoolInvestments\Pages;

use App\Filament\Admin\Resources\PoolInvestments\PoolInvestmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPoolInvestment extends EditRecord
{
    protected static string $resource = PoolInvestmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
